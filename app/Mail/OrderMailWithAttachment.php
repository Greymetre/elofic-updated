<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMailWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    public $attachmentPath;
    public $orderDetails;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachmentPath, $orderDetails, $order)
    {
        $this->attachmentPath = $attachmentPath;
        $this->orderDetails = $orderDetails;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.order-email-attech')
            ->attach($this->attachmentPath);
    }
}
