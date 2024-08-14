<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\EmailMailer;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // echo "<br/><br/>".__FILE__.'--<br/>'.__LINE__; dd($this->emailData);
            $mailObj = Mail::to($this->emailData['toEmail']);

            if( !empty($this->emailData['ccArray']) ){
                $mailObj->cc($this->emailData['ccArray']);
            }

            if( !empty($this->emailData['bccArray']) ){
                $mailObj->bcc($this->emailData['bccArray']);
            }

            $mailObj
                ->send(
                    new EmailMailer($this->emailData)
                );
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}