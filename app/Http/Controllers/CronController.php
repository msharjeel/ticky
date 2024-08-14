<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Helpers\Helper;
use App\Jobs\SendEmail;

use App\Http\Controllers\Controller;

// use App\Mail\EmailMailer;
// use Mail;

class CronController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function cron1NotifyAboutTicket()
    {
    	$NEW_STATUS_IDS = Helper::getStatusIds('NEW');
    	
    	// dd( [Date('Y-m-d h:i:s'), Helper::getPriorityExpiryTime($type)]);

    	$ticketsBundle = [];
		$types = ['Low', 'Medium', 'High', 'Urgent'];
		// $types = ['High'];//for testing
		foreach ($types as $type) {
			$tickets = Ticket::with([
						// 'ticketReplies',
						'ticketBody',
						'department' => function($qD){
							return $qD->with([
								'users' => function($qU){
												return $qU
														->whereNotNull('email')
														->with([
															'userRole'
														]);
											}
								]);
						}
					])
					->where('email_sent', '<', 1)
					->whereIn('status_id', $NEW_STATUS_IDS)
					->whereIn('priority_id', Helper::getPriorityIds($type) )
					->where('created_at', '<', Helper::getPriorityExpiryTime($type) )
					->get();

			$this->sendEmailAfterBccBundle($tickets, $type);

	    }//end foreach of tupes
    }
	
    private function sendEmailAfterBccBundle($tickets, $type){
    	foreach ($tickets as $ticket) {
			if( !empty($ticket->department) && 
				!empty($ticket->department->users) 				
			){

				$ticket->email_sent = 1;
				$ticket->save();

				//prepare cc/bcc emails bundle a/c ticket department users
				$bccArray = [];				
				foreach ($ticket->department->users as $user){
					if( !empty($user->email) ){
						$bccArray[] = $user->email;
					}
				}//end of foreach of user//
				
				$bccArray = array_unique($bccArray);

				if( sizeof($bccArray) > 0 ){
					$emailData = array();
					$emailData['toEmail'] = $bccArray[0];
					$emailData['type']  = $type;
					// $emailData['ccArray']  = $bccArray;
					$emailData['bccArray']  = $bccArray;
					$emailData['template_name'] = "cron1-notify-about-task";
					$emailData['subject'] = "Fault#$ticket->id need your consideration($ticket->subject)";//.date('Y-m-d h:i:s');
					$emailData['body']  = "Hi,";
					$emailData['body'] .= "<br/>Fault#$ticket->id need your consideration";
					$emailData['body'] .= "<br/><br/>Subject: ".$ticket->subject;
					if( !empty($ticket->ticketBody) && !empty($ticket->ticketBody[0]) ){
						$emailData['body'] .= "<br/><br/>Details: ".$ticket->ticketBody[0]->body;
					}
					$linkOfTicket = url("")."/dashboard/tickets/".$ticket->uuid."/manage";
					$emailData['body'] .= "<br/><br/>Link: <a href='".$linkOfTicket."'>".$linkOfTicket."</a>";

					$emailData['body'] .= "<br/><br/>Ticky,";
					$emailData['body'] .= "<br/><a href='".url("")."'>".url("")."</a>";
					$this->notifyAboutTaskExpiry($emailData);					
				}


			}//end of if
		}//end foreach of ticket
    }

	private function notifyAboutTaskExpiry($emailData){
		if(1){//run it forcefully
			//dispatch a/c to queue, if its empty then will be sent soon
			SendEmail::dispatch($emailData);

			//Without Queoue
			//Mail::to($emailData['toEmail'])
            //->send(
            //    new EmailForQueuing($emailData)
            //);
		}
		else{
			//dispatch with time dely
			$emailJob = (new SendEmail($emailData))->delay(Carbon::now()->addMinutes(1));
        	
        	dispatch($emailJob);
        }

        echo "<br/><br/>".__FILE__.'--<br/>'.__LINE__; dump($emailData);
        // dd('testing, stop loop');
    }

}
