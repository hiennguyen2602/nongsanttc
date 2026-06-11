<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactMail extends Mailable implements ShouldQueue, ShouldQueueAfterCommit
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message) {}

    public function envelope(): Envelope
    {
        $subject = filled($this->message->subject)
            ? $this->message->subject
            : 'Tin nhắn liên hệ mới';

        return new Envelope(
            subject: $subject . ' - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact.new',
            with: [
                'messageUrl' => route('admin.contact-messages.show', $this->message, absolute: true),
            ],
        );
    }
}
