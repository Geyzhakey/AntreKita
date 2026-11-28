<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AntreKitaNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public User $user,
        public string $triggerType,
        public string $bodyMessage,
    ) {}

    public function envelope(): Envelope
    {
        $unitCode = $this->booking->facilityUnit?->code ?? 'Unit';

        $subject = match ($this->triggerType) {
            'booking_confirmed' => "[AntreKita] Konfirmasi Booking {$unitCode}",
            'h_minus_5_active' => "[AntreKita] Pengingat H-5 Menit — Sesi {$unitCode}",
            'h_minus_5_waiting' => "[AntreKita] Giliran {$unitCode} Dimulai 5 Menit Lagi",
            'handover_ready' => "[AntreKita] Unit {$unitCode} Siap Digunakan!",
            'session_completed' => "[AntreKita] Sesi Selesai — {$unitCode}",
            default => "[AntreKita] Notifikasi Sesi {$unitCode}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.antrekita-notification',
        );
    }
}
