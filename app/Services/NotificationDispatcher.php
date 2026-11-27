<?php

namespace App\Services;

use App\Mail\AntreKitaNotificationMail;
use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationDispatcher
{
    public const LOCAL_WA_URL = 'http://127.0.0.1:3001';

    public static function getLocalWaStatus(): array
    {
        try {
            $res = Http::timeout(2)->get(self::LOCAL_WA_URL . '/status');
            if ($res->successful() && is_array($json = $res->json())) {
                return [
                    'running' => true,
                    'connected' => (bool) ($json['connected'] ?? false),
                    'status' => (string) ($json['status'] ?? 'idle'),
                    'qr' => $json['qr'] ?? null,
                    'phone' => $json['phone'] ?? null,
                    'pairingCode' => $json['pairingCode'] ?? null,
                    'lastError' => $json['lastError'] ?? null,
                ];
            }
        } catch (\Throwable) {
        }

        return [
            'running' => false,
            'connected' => false,
            'status' => 'offline',
            'qr' => null,
            'phone' => null,
            'pairingCode' => null,
            'lastError' => null,
        ];
    }

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

    public static function hasExternalWhatsAppConfig(): bool
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

    public static function isWhatsAppConfigured(): bool
    {
        if (self::hasExternalWhatsAppConfig()) {
            return true;
        }

        return self::getLocalWaStatus()['connected'] === true;
    }

    public static function applyRuntimeMailConfig(): void
    {
        $smtpUser = trim((string) (SystemSetting::getValue('smtp_username') ?? ''));
        $smtpPass = trim((string) (SystemSetting::getValue('smtp_password') ?? ''));

        if ($smtpUser !== '' && $smtpPass !== '') {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', SystemSetting::getValue('smtp_host', 'smtp.gmail.com') ?: 'smtp.gmail.com');
            Config::set('mail.mailers.smtp.port', (int) (SystemSetting::getValue('smtp_port', '587') ?: 587));
            Config::set('mail.mailers.smtp.scheme', 'tls');
            Config::set('mail.mailers.smtp.username', $smtpUser);
            Config::set('mail.mailers.smtp.password', $smtpPass);
            Config::set('mail.from.address', SystemSetting::getValue('smtp_from_address', $smtpUser) ?: $smtpUser);
            Config::set('mail.from.name', 'AntreKita');
            Mail::purge('smtp');
        }
    }

    public static function isEmailConfigured(): bool
    {
        self::applyRuntimeMailConfig();

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

        NotificationLog::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'channel' => 'account',
            'recipient' => $user->username ?: $user->name,
            'trigger_type' => $triggerType,
            'message' => $message,
            'status' => 'sent',
            'error_message' => null,
            'sent_at' => $now,
            'read_at' => null,
        ]);

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
        $digits = preg_replace('/[^\d]/', '', $recipient);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        if (! self::hasExternalWhatsAppConfig()) {
            $local = self::getLocalWaStatus();
            if ($local['connected']) {
                try {
                    $response = Http::timeout(10)->post(self::LOCAL_WA_URL . '/send', [
                        'to' => $digits,
                        'message' => $message,
                    ]);

                    if ($response->successful()) {
                        return ['sent', null];
                    }

                    $reason = (string) ($response->json('reason') ?? "HTTP {$response->status()}");
                    Log::warning('AntreKita local WA delivery failed', ['recipient' => $digits, 'reason' => $reason]);

                    return ['failed', $reason];
                } catch (\Throwable $e) {
                    return ['failed', mb_substr($e->getMessage(), 0, 220)];
                }
            }

            return ['unconfigured', 'Scan QR WhatsApp pada panel di atas untuk mengaktifkan pengiriman otomatis.'];
        }

        $cfg = self::getWhatsAppConfig();

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

    public function retryLog(NotificationLog $log): NotificationLog
    {
        if ($log->channel === 'whatsapp') {
            [$status, $error] = $this->sendWhatsApp($log->recipient, $log->message, $log->trigger_type);
            $log->update([
                'status' => $status,
                'error_message' => $error,
                'sent_at' => now(),
            ]);
        } elseif ($log->channel === 'email' && $log->booking && $log->user) {
            [$status, $error] = $this->sendEmail($log->booking, $log->user, $log->trigger_type, $log->message);
            $log->update([
                'status' => $status,
                'error_message' => $error,
                'sent_at' => now(),
            ]);
        }

        return $log->fresh();
    }

    public function retryPendingForUser(User $user): int
    {
        $waReady = self::isWhatsAppConfigured();
        $emailReady = self::isEmailConfigured();

        if (! $waReady && ! $emailReady) {
            return 0;
        }

        $channels = [];
        if ($waReady) {
            $channels[] = 'whatsapp';
        }
        if ($emailReady) {
            $channels[] = 'email';
        }

        $pending = NotificationLog::with(['booking', 'user'])
            ->where('user_id', $user->id)
            ->whereIn('channel', $channels)
            ->where('status', '!=', 'sent')
            ->get();

        $sentCount = 0;
        foreach ($pending as $log) {
            $updated = $this->retryLog($log);
            if ($updated->status === 'sent') {
                $sentCount++;
            }
        }

        return $sentCount;
    }
}
