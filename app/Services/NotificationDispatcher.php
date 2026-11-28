<?php

namespace App\Services;

use App\Mail\AntreKitaNotificationMail;
use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationDispatcher
{
    protected static ?bool $smtpReachableCache = null;

    public static function applyRuntimeMailConfig(): void
    {
        $smtpUser = trim((string) (SystemSetting::getValue('smtp_username') ?? ''));
        $smtpPass = trim((string) (SystemSetting::getValue('smtp_password') ?? ''));

        if ($smtpUser !== '' && $smtpPass !== '') {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', SystemSetting::getValue('smtp_host', 'smtp.gmail.com') ?: 'smtp.gmail.com');
            Config::set('mail.mailers.smtp.port', (int) (SystemSetting::getValue('smtp_port', '587') ?: 587));
            Config::set('mail.mailers.smtp.scheme', 'smtp');
            Config::set('mail.mailers.smtp.timeout', 4);
            Config::set('mail.mailers.smtp.username', $smtpUser);
            Config::set('mail.mailers.smtp.password', $smtpPass);
            Config::set('mail.from.address', SystemSetting::getValue('smtp_from_address', $smtpUser) ?: $smtpUser);
            Config::set('mail.from.name', 'AntreKita');
            Mail::purge('smtp');
        }
    }

    public static function isSmtpPortReachable(string $host, int $port, float $timeoutSeconds = 2.0): bool
    {
        if (app()->runningUnitTests() || config('mail.default') === 'array') {
            return true;
        }

        if (self::$smtpReachableCache !== null) {
            return self::$smtpReachableCache;
        }

        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);

        if ($socket === false) {
            return self::$smtpReachableCache = false;
        }

        @fclose($socket);

        return self::$smtpReachableCache = true;
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
    public function sendEmail(Booking $booking, User $user, string $triggerType, string $message): array
    {
        if (! self::isEmailConfigured()) {
            return ['unconfigured', 'MAIL_MAILER masih mode log atau kredensial SMTP di .env belum diisi.'];
        }

        if (config('mail.default') === 'smtp') {
            $host = (string) config('mail.mailers.smtp.host', '');
            $port = (int) config('mail.mailers.smtp.port', 587);

            if ($host !== '' && ! self::isSmtpPortReachable($host, $port, 2.0)) {
                Log::warning('AntreKita SMTP port unreachable', ['host' => $host, 'port' => $port, 'email' => $user->email]);

                return ['failed', "Koneksi SMTP ke {$host}:{$port} diblokir jaringan cloud atau timeout. Pesan tetap tersimpan di Kotak Masuk Akun."];
            }
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
        if ($log->channel === 'email' && $log->booking && $log->user) {
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
        if (! self::isEmailConfigured()) {
            return 0;
        }

        $pending = NotificationLog::with(['booking', 'user'])
            ->where('user_id', $user->id)
            ->where('channel', 'email')
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
