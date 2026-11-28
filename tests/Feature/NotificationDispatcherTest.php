<?php

namespace Tests\Feature;

use App\Mail\AntreKitaNotificationMail;
use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\Institution;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationDispatcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_email_and_account_notifications_with_exact_templates(): void
    {
        Mail::fake();

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.username', 'antrekita@gmail.com');
        Config::set('mail.mailers.smtp.password', 'AntreKita Informatika!');

        $admin = User::factory()->create(['role' => 'admin']);
        $institution = Institution::create([
            'admin_id' => $admin->id,
            'name' => 'PUT Politeknik Negeri Jakarta',
            'slug' => 'put-pnj',
            'registration_code' => 'PUT1PNJ26',
            'city' => 'Depok',
            'address' => 'Jl. Prof. DR. G.A. Siwabessy',
            'is_active' => true,
        ]);

        $unit = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'LT1-L2',
            'name' => 'Meja Leseh L2',
            'zone' => 'leseh_desk',
            'seat_type' => 'L',
            'capacity' => 4,
            'max_duration_minutes' => 120,
            'is_active' => true,
        ]);

        $visitor = User::factory()->create([
            'role' => 'visitor',
            'name' => 'Dandy Aulia',
            'email' => 'dandy@example.com',
        ]);

        $booking = Booking::create([
            'institution_id' => $institution->id,
            'facility_unit_id' => $unit->id,
            'user_id' => $visitor->id,
            'group_name' => 'dandy_aulia',
            'people_count' => 2,
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(5),
            'finished_at' => now()->addMinutes(5),
            'status' => 'active',
        ]);

        $dispatcher = app(NotificationDispatcher::class);
        $dispatcher->dispatchDualChannel($booking, $visitor, 'h_minus_5_active', 'Sisa 5 menit');
        $dispatcher->dispatchDualChannel($booking, $visitor, 'session_completed', 'Sesi selesai');

        Mail::assertSent(AntreKitaNotificationMail::class, function (AntreKitaNotificationMail $mail) {
            if ($mail->triggerType === 'h_minus_5_active') {
                $mail->assertHasSubject('ANTREKITA: 5 MENIT MASA PENGGUNAAN MEJA LESEH L2 (LT1-L2) KAMU!');
                $html = $mail->render();

                return str_contains($html, 'Halo, <strong>Dandy Aulia</strong>!')
                    && str_contains($html, 'PUT Politeknik Negeri Jakarta')
                    && str_contains($html, 'Tim AntreKita');
            }

            if ($mail->triggerType === 'session_completed') {
                $mail->assertHasSubject('ANTREKITA: MASA PENGGUNAAN MEJA LESEH L2 (LT1-L2) TELAH SELESAI');
                $html = $mail->render();

                return str_contains($html, 'Selesai')
                    && str_contains($html, 'Terima kasih telah menjadi bagian dari pengguna AntreKita.')
                    && str_contains($html, 'Tim AntreKita');
            }

            return false;
        });

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'account',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'email',
            'status' => 'sent',
        ]);

        $this->assertDatabaseMissing('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'whatsapp',
        ]);
    }

    public function test_marks_email_unconfigured_when_mailer_has_no_credentials(): void
    {
        Config::set('mail.default', 'log');
        SystemSetting::setValue('smtp_username', '');
        SystemSetting::setValue('smtp_password', '');

        $admin = User::factory()->create(['role' => 'admin']);
        $institution = Institution::create([
            'admin_id' => $admin->id,
            'name' => 'PUT PNJ',
            'slug' => 'put-pnj-2',
            'registration_code' => 'PUT2PNJ26',
            'city' => 'Depok',
            'address' => 'Kampus PNJ',
            'is_active' => true,
        ]);

        $unit = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'LT1-1',
            'name' => 'Meja 1',
            'zone' => 'standard_desk',
            'capacity' => 2,
            'max_duration_minutes' => 120,
            'is_active' => true,
        ]);

        $visitor = User::factory()->create([
            'role' => 'visitor',
            'email' => 'user@example.com',
        ]);

        $booking = Booking::create([
            'institution_id' => $institution->id,
            'facility_unit_id' => $unit->id,
            'user_id' => $visitor->id,
            'group_name' => 'user',
            'people_count' => 1,
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(30),
            'status' => 'active',
        ]);

        app(NotificationDispatcher::class)->dispatchDualChannel(
            $booking,
            $visitor,
            'booking_confirmed',
            'Test'
        );

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'account',
            'status' => 'sent',
        ]);

        $this->assertSame(1, NotificationLog::where('status', 'unconfigured')->count());
    }
}
