<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Conference;
use App\Models\Registration;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Conference $conference;
    public Registration $registration;
    public Payment $payment;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Conference $conference, Registration $registration, Payment $payment)
    {
        $this->user = $user;
        $this->conference = $conference;
        $this->registration = $registration;
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FULafia Conference - Registration Confirmed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_confirmed',
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
