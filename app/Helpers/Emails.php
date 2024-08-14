<?php

namespace App\Helpers;

use App\Mail\EmailForQueuing;
use Illuminate\Support\Facades\Mail;

class Emails
{

	public static function cron1NotifyAboutTicket($emailData)
    {
        try {
        	$emailData['template_name'] = $emailData['template_name'] ?? 'cron1-notify-about-task';
            // dd($emailData);
            Mail::to($emailData['toEmail'])
            	->send(
            		new EmailForQueuing($emailData)
            	);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
?>