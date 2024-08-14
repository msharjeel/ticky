<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $template_name = 'default';
    public $fromEmail =  'do-not-reply@ticky1.weareone-bh.org';
    public $fromName = 'TICKY';
    
    public $toEmail = '';
    public $toName = '';
    public $subject = '';
    public $body = '';

    public $emailData = [];

    /**
     * Create a new message instance.
     *
     * @param array $emailData in $key=>$value for replace {params} to data
     */
    public function __construct(array $emailData = [])
    {
        $this->emailData = $emailData;

        //Required
        $this->template_name = $emailData['template_name'];
        $this->toEmail = $emailData['toEmail'];

        //Optional - Will be filled with empty if not found
        $this->toName = $emailData['toName'] ?? '';
        $this->subject = $emailData['subject'] ?? '';
        $this->body = $emailData['body'] ?? '';

        //Optional - Will be filled with default value if not found
        $this->fromEmail = !empty($emailData['fromEmail']) ? $emailData['fromEmail'] : $this->fromEmail;
        $this->fromName =  !empty($emailData['fromName'])  ? $emailData['fromName'] : $this->fromName;

        // echo __FILE__.'--'.__LINE__; dd($emailData);
        
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        try {            
            // echo __FILE__.'--'.__LINE__; dd($emailData);
            return $this->view('emails.'.$this->template_name)
                    ->from($this->fromEmail, $this->fromName)
                    ->subject($this->subject)
                    ->with(['emailData' => $this->emailData]);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
