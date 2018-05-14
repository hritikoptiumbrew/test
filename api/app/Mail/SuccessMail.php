<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $title,$msg,$subject;
    public function __construct($title,$sub,$msg)
    {
        $this->title = $title;
        $this->subject = $sub;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title= $this->title;
        $sub= $this->subject;
        $msg= $this->msg;
        return $this->markdown('emails.success',compact('title','msg','sub'))->subject($sub);
    }
}
