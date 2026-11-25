<?php

namespace App\Mail;

use App\Models\SalesDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SalesDocument $document) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel : facture '.$this->document->reference.' en attente de règlement',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-reminder',
        );
    }
}
