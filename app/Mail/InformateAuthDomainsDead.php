<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformateAuthDomainsDead extends Mailable
{
    use Queueable, SerializesModels;
    public $urls;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($urls)
    {
        //
        $this->urls = $urls;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.informateAuthDomainsDead',[
            'urls'=>$this->urls
        ]);
    }
}
