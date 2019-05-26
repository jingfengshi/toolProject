<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InformateLandDomainDead extends Mailable
{
    use Queueable, SerializesModels;
    protected $urls;

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
        return $this->markdown('emails.infomateLandDomainDead',[
            'urls'=>$this->urls
        ]);
    }
}
