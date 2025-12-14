<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class Laporan6BulananMail extends Mailable
{
    use Queueable, SerializesModels;

    public $laporanData;
    public $startDate;
    public $endDate;

    /**
     * Create a new message instance.
     */
    public function __construct($laporanData, $startDate, $endDate)
    {
        $this->laporanData = $laporanData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $periodLabel = $this->startDate->locale('id')->format('d F Y') . ' - ' . $this->endDate->locale('id')->format('d F Y');
        return new Envelope(
            subject: 'Laporan 6 Bulanan - ' . $periodLabel,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.laporan-6bulanan',
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

