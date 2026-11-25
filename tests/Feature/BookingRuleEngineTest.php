<?php

namespace Tests\Feature;

use App\Models\FacilityUnit;
use App\Models\Institution;
use App\Models\User;
use App\Services\BookingRuleEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingRuleEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_anti_monopoly_blocks_second_active_or_waiting_booking(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_verified_admin' => true,
            'whatsapp_number' => '+6281200000001',
        ]);

        $institution = Institution::create([
            'admin_id' => $admin->id,
            'name' => 'Perpustakaan UI',
            'slug' => 'perpus-ui',
            'city' => 'Depok',
            'address' => 'Kampus UI',
            'is_active' => true,
        ]);

        $unit1 = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'Meja09',
            'name' => 'Meja Pribadi Outdoor 4',
            'zone' => 'outdoor_desk',
            'capacity' => 1,
            'max_duration_minutes' => 180,
            'is_active' => true,
        ]);

        $unit2 = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'Meja10',
            'name' => 'Meja Pribadi Outdoor 5',
            'zone' => 'outdoor_desk',
            'capacity' => 1,
            'max_duration_minutes' => 180,
            'is_active' => true,
        ]);

        $visitor = User::factory()->create([
            'role' => 'visitor',
            'whatsapp_number' => '+6281299990001',
        ]);

        $engine = app(BookingRuleEngine::class);
        $now = now();

        $engine->createBooking($visitor, $unit1, 'Dandy Aulia', 1, $now, $now->copy()->addMinutes(60));

        $this->expectException(ValidationException::class);
        $engine->createBooking($visitor, $unit2, 'Dandy Aulia', 1, $now->copy()->addMinutes(5), $now->copy()->addMinutes(45));
    }

    public function test_h_minus_5_dual_trigger_and_slide_to_finish_cascade(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_verified_admin' => true,
            'whatsapp_number' => '+6281200000002',
        ]);

        $institution = Institution::create([
            'admin_id' => $admin->id,
            'name' => 'PUT PNJ',
            'slug' => 'put-pnj',
            'city' => 'Depok',
            'address' => 'Kampus PNJ',
            'is_active' => true,
        ]);

        $unit = FacilityUnit::create([
            'institution_id' => $institution->id,
            'code' => 'Ruang03',
            'name' => 'Ruang Diskusi 3',
            'zone' => 'group_room',
            'capacity' => 6,
            'max_duration_minutes' => 180,
            'is_active' => true,
        ]);

        $user1 = User::factory()->create(['role' => 'visitor', 'whatsapp_number' => '+6281211110001']);
        $user2 = User::factory()->create(['role' => 'visitor', 'whatsapp_number' => '+6281211110002']);

        $engine = app(BookingRuleEngine::class);
        $now = now();

        $b1 = $engine->createBooking($user1, $unit, 'Kelompok A', 4, $now->copy()->subMinutes(56), $now->copy()->addMinutes(4));
        $b2 = $engine->createBooking($user2, $unit, 'Kelompok B', 3, $now->copy()->addMinutes(4), $now->copy()->addMinutes(64));

        $this->assertSame('active', $b1->status);
        $this->assertSame('waiting', $b2->status);

        $alerts = $engine->processPreExpiryAlerts($now);
        $this->assertCount(2, $alerts);

        $result = $engine->slideToFinish($b1->fresh(), $user1);
        $this->assertNotNull($result['promoted']);
        $this->assertSame('active', $b2->fresh()->status);
        $this->assertSame('completed', $b1->fresh()->status);
    }
}
