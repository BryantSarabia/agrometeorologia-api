<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PestReports extends Mailable
{
    use Queueable, SerializesModels;

     public $user, $reports;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $reports)
    {
         $this->user = $user;
         $this->reports = $reports;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reports');
    }
}
