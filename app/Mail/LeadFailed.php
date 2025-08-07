<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;

class LeadFailed extends Mailable
{
    use Queueable, SerializesModels;

    public Lead $lead;

    /**
     * Create a new message instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function build() {
        return $this
            ->subject('Submission failure')
            ->markdown('mail.lead-failed')
            ->with([
                'lead' => $this->lead
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lead Failed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.lead-failed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
