<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Conference;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FullPaperSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Conference $conference;
    public Submission $submission;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Conference $conference, Submission $submission)
    {
        $this->user = $user;
        $this->conference = $conference;
        $this->submission = $submission;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FULafia Conference - Full Paper Manuscript Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.full_paper_submitted',
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
