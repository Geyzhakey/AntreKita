<?php

namespace App\Services;

use App\Mail\AntreKitaNotificationMail;
use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationDispatcher
{
    public static function getWhatsAppConfig(): array
    {
        $driver = SystemSetting::getValue('whatsapp_driver') ?: config('services.whatsapp.driver', 'fonnte');
        $token = SystemSetting::getValue('whatsapp_api_token') ?: config('services.whatsapp.token');
        $url = SystemSetting::getValue('whatsapp_api_url') ?: config('services.whatsapp.url');
        $phoneId = SystemSetting::getValue('whatsapp_phone_number_id') ?: config('services.whatsapp.phone_number_id');
        $session = SystemSetting::getValue('whatsapp_session') ?: config('services.whatsapp.session', 'default');

        return [
            'driver' => strtolower(trim((string) $driver)),
            'token' => trim((string) $token),
            'url' => trim((string) $url),
            'phone_number_id' => trim((string) $phoneId),
            'session' => trim((string) $session) ?: 'default',
        ];
    }

    public static function isWhatsAppConfigured(): bool
    {
        $cfg = self::getWhatsAppConfig();

        if ($cfg['driver'] === 'webhook' || $cfg['driver'] === 'waha') {
            return $cfg['url'] !== '';
        }

        if ($cfg['driver'] === 'meta') {
            return $cfg['token'] !== '' && $cfg['phone_number_id'] !== '';
        }

        return $cfg['token'] !== '';
    }

    public static function isEmailConfigured(): bool
    {
        $mailer = (string) config('mail.default', 'log');
        if (in_array($mailer, ['log', 'array'], true)) {
            return false;
        }

        if ($mailer === 'smtp') {
            $host = (string) config('mail.mailers.smtp.host', '');
            $username = (string) config('mail.mailers.smtp.username', '');
            $password = (string) config('mail.mailers.smtp.password', '');

            if ($host === '' || ($host === '127.0.0.1' && $username === '') || ($username === '' && $password === '')) {
                return false;
            }
        }

        return true;
    }

    public function dispatchDualChannel(Booking $booking, User $user, string $triggerType, string $message): void
    {
        $now = now();

        if ($user->whatsapp_number) {
            [$waStatus, $waError] = $this->sendWhatsApp($user->whatsapp_number, $message, $triggerType);

            NotificationLog::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'channel' => 'whatsapp',
                'recipient' => $user->whatsapp_number,
                'trigger_type' => $triggerType,
                'message' => $message,
                'status' => $waStatus,
                'error_message' => $waError,
                'sent_at' => $now,
            ]);
        }

        if ($user->email) {
            [$mailStatus, $mailError] = $this->sendEmail($booking, $user, $triggerType, $message);

            NotificationLog::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'channel' => 'email',
                'recipient' => $user->email,
                'trigger_type' => $triggerType,
                'message' => $message,
                'status' => $mailStatus,
                'error_message' => $mailError,
                'sent_at' => $now,
            ]);
        }
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    public function sendWhatsApp(string $recipient, string $message, string $triggerType = 'notification'): array
    {
        if (! self::isWhatsAppConfigured()) {
            return ['unconfigured', 'Token/URL WhatsApp Gateway belum diisi di .env atau Pengaturan Developer.'];
        }

        $cfg = self::getWhatsAppConfig();
        $digits = preg_replace('/[^\d]/', '', $recipient);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        try {
            $response = match ($cfg['driver']) {
                'wablas' => Http::timeout(10)
                    ->withHeaders(['Authorization' => $cfg['token']])
                    ->post($cfg['url'] ?: 'https://pati.wablas.com/api/send-message', [
                        'phone' => $digits,
                        'message' => $message,
                    ]),

                'waha' => Http::timeout(10)
                    ->withHeaders($cfg['token'] !== '' ? ['X-Api-Key' => $cfg['token']] : [])
                    ->post(rtrim($cfg['url'], '/') . '/api/sendText', [
                        'chatId' => $digits . '@c.us',
                        'text' => $message,
                        'session' => $cfg['session'],
                    ]),

                'meta' => Http::timeout(10)
                    ->withToken($cfg['token'])
                    ->post("https://graph.facebook.com/v20.0/{$cfg['phone_number_id']}/messages", [
                        'messaging_product' => 'whatsapp',
                        'to' => $digits,
                        'type' => 'text',
                        'text' => ['body' => $message],
                    ]),

                'webhook' => Http::timeout(10)
                    ->withHeaders($cfg['token'] !== '' ? ['Authorization' => 'Bearer ' . $cfg['token']] : [])
                    ->post($cfg['url'], [
                        'to' => $recipient,
                        'digits' => $digits,
                        'message' => $message,
                        'trigger' => $triggerType,
                    ]),

                default => Http::timeout(10)
                    ->withHeaders(['Authorization' => $cfg['token']])
                    ->asForm()
                    ->post($cfg['url'] ?: 'https://api.fonnte.com/send', [
                        'target' => $digits,
                        'message' => $message,
                        'countryCode' => '62',
                    ]),
            };

            if (! $response->successful()) {
                $err = "HTTP {$response->status()}: " . mb_substr($response->body(), 0, 200);
                Log::warning('AntreKita WhatsApp delivery failed', ['recipient' => $digits, 'error' => $err]);

                return ['failed', $err];
            }

            $json = $response->json();
            if (is_array($json) && array_key_exists('status', $json) && $json['status'] === false) {
                $reason = (string) ($json['reason'] ?? $json['message'] ?? 'Ditolak oleh gateway.');
                Log::warning('AntreKita WhatsApp API rejected', ['recipient' => $digits, 'reason' => $reason]);

                return ['failed', $reason];
            }

            return ['sent', null];
        } catch (\Throwable $e) {
            Log::error('AntreKita WhatsApp exception', ['recipient' => $digits, 'exception' => $e->getMessage()]);

            return ['failed', mb_substr($e->getMessage(), 0, 220)];
        }
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    public function sendEmail(Booking $booking, User $user, string $triggerType, string $message): array
    {
        if (! self::isEmailConfigured()) {
            return ['unconfigured', 'MAIL_MAILER masih mode log atau kredensial SMTP di .env belum diisi.'];
        }

        try {
            $booking->loadMissing(['facilityUnit', 'institution']);
            Mail::to($user->email)->send(new AntreKitaNotificationMail($booking, $user, $triggerType, $message));

            return ['sent', null];
        } catch (\Throwable $e) {
            Log::error('AntreKita Email delivery failed', ['email' => $user->email, 'exception' => $e->getMessage()]);

            return ['failed', mb_substr($e->getMessage(), 0, 220)];
        }
    }
}
