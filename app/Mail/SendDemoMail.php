<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDemoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $maildata;
    /**
     * Create a new message instance.
     */
    public function __construct($maildata)
    {
        $this->maildata = $maildata;
        Log::info('Mail data: ' . json_encode($maildata));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->maildata['title'].' - '.time(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sendDemoMail',
            with: [
                'title' => $this->maildata['title'],
                'url' => $this->maildata['url'],
                'client_name' => $this->maildata['client_name'],
                'client_document' => $this->maildata['client_document'],
                'order_code' => $this->maildata['order_code'],
                'order_date' => $this->maildata['order_date'],
                'order_subtotal' => $this->maildata['order_subtotal'],
                'order_igv' => $this->maildata['order_igv'],
                'order_total' => $this->maildata['order_total'],
                'order_status' => $this->maildata['order_status'],
                'order_items' => $this->maildata['order_items'],
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Attachment::fromStorage('public/attachment.pdf', 'attachment.pdf'),
        return [
            // Attachment::fromPath('https://images.pexels.com/photos/4888482/pexels-photo-4888482.jpeg')
            //     ->as('attachment.jpeg')
            //     ->withMime('image/jpeg'),
        ];
    }
}
