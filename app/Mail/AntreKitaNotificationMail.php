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

    public string $unitName;

    public string $unitCode;

    public string $unitKind;

    public string $unitDisplay;

    public string $locationName;

    public function __construct(
        public Booking $booking,
        public User $user,
        public string $triggerType,
        public string $bodyMessage,
    ) {
        $unit = $this->booking->facilityUnit;
        $this->unitName = $unit?->name ?: ($unit?->code ?? 'Unit');
        $this->unitCode = $unit?->code ?? '';
        $this->unitKind = ($unit?->seat_type === 'ROOM' || $unit?->zone === 'group_room') ? 'Ruangan' : 'Meja';
        $this->unitDisplay = $this->unitCode !== '' && $this->unitCode !== $this->unitName
            ? "{$this->unitName} ({$this->unitCode})"
            : $this->unitName;
        $this->locationName = $this->booking->institution?->name ?? 'Lokasi Mitra AntreKita';
    }

    public function envelope(): Envelope
    {
        $upperUnit = mb_strtoupper($this->unitDisplay);

        $subject = match ($this->triggerType) {
            'h_minus_5_active' => "ANTREKITA: 5 MENIT MASA PENGGUNAAN {$upperUnit} KAMU!",
            'session_completed' => "ANTREKITA: MASA PENGGUNAAN {$upperUnit} TELAH SELESAI",
            'booking_confirmed' => "ANTREKITA: KONFIRMASI RESERVASI {$upperUnit}",
            'h_minus_5_waiting' => "ANTREKITA: GILIRAN {$upperUnit} DIMULAI 5 MENIT LAGI",
            'handover_ready' => "ANTREKITA: {$upperUnit} SIAP DIGUNAKAN!",
            default => "ANTREKITA: NOTIFIKASI SESI {$upperUnit}",
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
