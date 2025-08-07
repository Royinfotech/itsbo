<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentNewPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $newPassword)
    {
        $this->student = $student;
        $this->newPassword = $newPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your New Password - Student Portal',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-new-password',
            with: [
                'studentName' => $this->student->name ?? $this->student->first_name . ' ' . $this->student->last_name,
                'newPassword' => $this->newPassword,
                'loginUrl' => url('/student/login') // Adjust this URL as needed
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
        return [];
    }
}