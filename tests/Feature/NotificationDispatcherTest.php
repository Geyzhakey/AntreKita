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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationDispatcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_real_email_and_whatsapp_when_configured(): void
    {
        Mail::fake();
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true, 'id' => 'wa-123'], 200),
        ]);

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.username', 'antrekita@gmail.com');
        Config::set('mail.mailers.smtp.password', 'app-password');

        SystemSetting::setValue('whatsapp_driver', 'fonnte');
        SystemSetting::setValue('whatsapp_api_token', 'fonnte-live-token');

        $admin = User::factory()->create(['role' => 'admin']);
        $institution = Institution::create([
            'name' => 'PUT PNJ',
            'slug' => 'put-pnj',
            'registration_code' => 'PUT1PNJ26',
            'city' => 'Depok',
            'address' => 'Jl. Prof. DR. G.A. Siwabessy',
            'admin_id' => $admin->id,
            'is_active' => true,
        ]);

        $unit = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'LT1-L2',
            'name' => 'Meja Leseh L2',
            'zone' => 'Lantai 1',
            'type' => 'leseh',
            'capacity' => 4,
            'max_duration_minutes' => 120,
            'is_active' => true,
        ]);

        $visitor = User::factory()->create([
            'role' => 'visitor',
            'email' => 'dandy@example.com',
            'whatsapp_number' => '+6281222822531',
        ]);

        $booking = Booking::create([
            'institution_id' => $institution->id,
            'facility_unit_id' => $unit->id,
            'user_id' => $visitor->id,
            'group_name' => 'dandy_aulia1',
            'people_count' => 2,
            'starts_at' => now(),
            'ends_at' => now()->addMinutes(30),
            'status' => 'active',
        ]);

        app(NotificationDispatcher::class)->dispatchDualChannel(
            $booking,
            $visitor,
            'booking_confirmed',
            'Booking LT1-L2 dikonfirmasi.'
        );

        Mail::assertSent(AntreKitaNotificationMail::class, fn ($mail) => $mail->hasTo('dandy@example.com'));

        Http::assertSent(fn ($request) => $request->url() === 'https://api.fonnte.com/send'
            && $request['target'] === '6281222822531'
            && $request['message'] === 'Booking LT1-L2 dikonfirmasi.');

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'account',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'whatsapp',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $visitor->id,
            'channel' => 'email',
            'status' => 'sent',
        ]);
    }

    public function test_marks_unconfigured_when_gateways_have_no_credentials(): void
    {
        Config::set('mail.default', 'log');
        SystemSetting::setValue('whatsapp_api_token', '');
        Config::set('services.whatsapp.token', '');

        $admin = User::factory()->create(['role' => 'admin']);
        $institution = Institution::create([
            'name' => 'PUT PNJ',
            'slug' => 'put-pnj-2',
            'registration_code' => 'PUT2PNJ26',
            'city' => 'Depok',
            'address' => 'Kampus PNJ',
            'admin_id' => $admin->id,
            'is_active' => true,
        ]);

        $unit = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'LT1-1',
            'name' => 'Meja 1',
            'zone' => 'Lantai 1',
            'type' => 'desk',
            'capacity' => 2,
            'max_duration_minutes' => 120,
            'is_active' => true,
        ]);

        $visitor = User::factory()->create([
            'role' => 'visitor',
            'email' => 'user@example.com',
            'whatsapp_number' => '+6281200001111',
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

        $this->assertSame(2, NotificationLog::where('status', 'unconfigured')->count());
    }
}
