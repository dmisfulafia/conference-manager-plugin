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

class SubmissionReviewed extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Conference $conference;
    public Submission $submission;
    public string $type;
    public string $status;
    public ?string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Conference $conference, Submission $submission, string $type, string $status, ?string $reason = null)
    {
        $this->user = $user;
        $this->conference = $conference;
        $this->submission = $submission;
        $this->type = $type;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $typeLabel = $this->type === 'abstract' ? 'Abstract Proposal' : 'Full Paper';
        $statusLabel = $this->status === 'approved' ? 'Approved' : 'Revision Required';
        
        return new Envelope(
            subject: "FULafia Conference - {$typeLabel} Review Update: {$statusLabel}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission_reviewed',
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
