<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\DeveloperAuditLog;
use App\Models\FacilityUnit;
use App\Models\Institution;
use App\Models\InstitutionWhitelist;
use App\Models\NotificationLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 0. Global System Settings
        SystemSetting::setValue('developer_whatsapp', '081222822531');
        SystemSetting::setValue('global_announcement', '');
        SystemSetting::setValue('booking_maintenance_mode', '0');

        // 1. 4 Developer Accounts (2 Utama + 2 Cadangan) — Instansi: Developer.Ai
        $devConfigs = [
            [
                'name' => 'Developer Utama 01 (Dandy)',
                'username' => 'dev_utama_01',
                'email' => 'dev.utama1@developer.ai',
                'wa' => '+628110000001',
                'code' => 'DEV-MAIN-01',
                'pass' => 'DevAntreKita#2026',
                'purpose' => 'Akun Utama Otoritas Tertinggi Platform AntreKita',
            ],
            [
                'name' => 'Developer Utama 02 (Lead Ops)',
                'username' => 'dev_utama_02',
                'email' => 'dev.utama2@developer.ai',
                'wa' => '+628110000002',
                'code' => 'DEV-MAIN-02',
                'pass' => 'DevAntreKita#2026',
                'purpose' => 'Akun Utama Verifikasi Instansi & Audit Sistem',
            ],
            [
                'name' => 'Developer Cadangan 01',
                'username' => 'dev_cadangan_01',
                'email' => 'dev.cadangan1@developer.ai',
                'wa' => '+628110000003',
                'code' => 'DEV-BKP-01',
                'pass' => 'DevBackup#2026',
                'purpose' => 'Akun Cadangan Darurat Otoritas Developer',
            ],
            [
                'name' => 'Developer Cadangan 02',
                'username' => 'dev_cadangan_02',
                'email' => 'dev.cadangan2@developer.ai',
                'wa' => '+628110000004',
                'code' => 'DEV-BKP-02',
                'pass' => 'DevBackup#2026',
                'purpose' => 'Akun Cadangan Pemulihan Sistem AntreKita',
            ],
        ];

        $primaryDev = null;
        foreach ($devConfigs as $dev) {
            $created = User::updateOrCreate(
                ['email' => $dev['email']],
                [
                    'name' => $dev['name'],
                    'username' => $dev['username'],
                    'affiliation' => 'Developer.Ai',
                    'whatsapp_number' => $dev['wa'],
                    'role' => 'developer',
                    'registration_code' => $dev['code'],
                    'institution_label' => 'Developer.Ai',
                    'purpose' => $dev['purpose'],
                    'is_verified_admin' => true,
                    'is_frozen' => false,
                    'password' => Hash::make($dev['pass']),
                ]
            );
            $primaryDev ??= $created;
        }

        // 2. Seed PUT Politeknik Negeri Jakarta (PUT1PNJ26) with Exact 2-Floor Blueprint
        $pnjAdmin = $this->seedAdminAndInstitution(
            $primaryDev,
            $now,
            [
                'email' => 'put.pnj@pnj.ac.id',
                'username' => 'admin_pnj',
                'admin_name' => 'Admin PUT PNJ',
                'wa' => '+6281200001002',
                'reg_code' => 'PUT1PNJ26',
                'name' => 'PUT Politeknik Negeri Jakarta',
                'slug' => 'put-pnj',
                'city' => 'Depok',
                'building_type' => 'Gedung Layanan & Study Hub',
                'address' => 'Jl. Prof. DR. G.A. Siwabessy, Kampus UI Depok',
                'description' => 'Pusat Layanan Terpadu & ruang belajar kolaboratif 2 lantai Politeknik Negeri Jakarta — terbuka inklusif untuk sivitas akademika & pengunjung.',
                'purpose' => 'Pemetaan presisi 2 lantai PUT PNJ (Meja Leseh L, Standar S, Tinggi H, Ruang Fokus, dan Ruang Diskusi).',
                'has_custom_floorplan' => true,
                'map_cols' => 8,
                'map_rows' => 6,
            ]
        );
        $putPnj = $pnjAdmin['institution'];
        $this->seedPutPnjUnits($putPnj);

        // 3. Seed Perpustakaan Universitas Indonesia (LBR1UI26)
        $uiAdmin = $this->seedAdminAndInstitution(
            $primaryDev,
            $now,
            [
                'email' => 'perpustakaanui1@gmail.ac.id',
                'username' => 'admin_ui',
                'admin_name' => 'Admin Perpustakaan UI',
                'wa' => '+6281200001001',
                'reg_code' => ' ',
                'name' => 'Perpustakaan Universitas Indonesia',
                'slug' => 'perpus-ui',
                'city' => 'Depok',
                'building_type' => 'Perpustakaan Kampus',
                'address' => 'Kampus UI Depok, Jawa Barat',
                'description' => 'Perpustakaan pusat (The Crystal of Knowledge) dengan ruang diskusi kelompok, meja baca indoor, dan area outdoor tepi danau.',
                'purpose' => 'Manajemen antrean ruang diskusi dan meja baca mahasiswa Universitas Indonesia.',
                'has_custom_floorplan' => false,
                'map_cols' => 6,
                'map_rows' => 4,
            ]
        );
        $perpusUi = $uiAdmin['institution'];
        $this->seedStandardGridUnits($perpusUi, groups: 3, indoor: 5, outdoor: 5);

        // 4. Visitors with Nama Pengguna (username) + Instansi (affiliation)
        $user1 = User::updateOrCreate(
            ['email' => 'pengguna1@gmail.com'],
            [
                'name' => 'Dandy Aulia',
                'username' => 'dandy_aulia',
                'affiliation' => 'Politeknik Negeri Jakarta',
                'whatsapp_number' => '+6281211112222',
                'role' => 'visitor',
                'is_verified_admin' => false,
                'password' => Hash::make('password'),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'pengguna2@gmail.com'],
            [
                'name' => 'Geyzha Keyriz',
                'username' => 'geyzha_k',
                'affiliation' => 'Universitas Indonesia',
                'whatsapp_number' => '+6281233334444',
                'role' => 'visitor',
                'is_verified_admin' => false,
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'pengguna3@gmail.com'],
            [
                'name' => 'Alif Fhatur Rouchman',
                'username' => 'alif_fhatur',
                'affiliation' => 'Politeknik Negeri Jakarta',
                'whatsapp_number' => '+6281255556666',
                'role' => 'visitor',
                'is_verified_admin' => false,
                'password' => Hash::make('password'),
            ]
        );

        $user4 = User::updateOrCreate(
            ['email' => 'pengguna4@gmail.com'],
            [
                'name' => 'Nadia Putri',
                'username' => 'nadia_putri',
                'affiliation' => 'Politeknik Negeri Jakarta',
                'whatsapp_number' => '+6281277778888',
                'role' => 'visitor',
                'is_verified_admin' => false,
                'password' => Hash::make('password'),
            ]
        );

        // 5. Pre-seeded Realistic Scenario on PUT PNJ Lantai 1 (LT1-9) & Lantai 2 (LT2-S1)
        $lt1Meja9 = FacilityUnit::where('institution_id', $putPnj->id)->where('code', 'LT1-9')->first();

        if ($lt1Meja9 && ! Booking::where('facility_unit_id', $lt1Meja9->id)->whereIn('status', ['active', 'waiting'])->exists()) {
            $activeStart = $now->copy()->subMinutes(40);
            $activeEnd = $now->copy()->addMinutes(55);

            $activeBooking = Booking::create([
                'institution_id' => $putPnj->id,
                'facility_unit_id' => $lt1Meja9->id,
                'user_id' => $user1->id,
                'group_name' => 'Dandy Aulia (Individu)',
                'people_count' => 1,
                'starts_at' => $activeStart,
                'ends_at' => $activeEnd,
                'status' => 'active',
            ]);

            NotificationLog::create([
                'booking_id' => $activeBooking->id,
                'user_id' => $user1->id,
                'channel' => 'whatsapp',
                'recipient' => $user1->whatsapp_number,
                'trigger_type' => 'booking_confirmed',
                'message' => "Booking LT1-9 (Meja Standar 9 — Lantai 1 PUT PNJ) dikonfirmasi untuk pukul {$activeStart->format('H:i')} - {$activeEnd->format('H:i')}.",
                'sent_at' => $activeStart,
            ]);

            $waitStart = $activeEnd->copy();
            $waitEnd = $waitStart->copy()->addMinutes(90);

            $waitingBooking = Booking::create([
                'institution_id' => $putPnj->id,
                'facility_unit_id' => $lt1Meja9->id,
                'user_id' => $user2->id,
                'group_name' => 'Geyzha Keyriz (UI Depok)',
                'people_count' => 1,
                'starts_at' => $waitStart,
                'ends_at' => $waitEnd,
                'status' => 'waiting',
            ]);

            NotificationLog::create([
                'booking_id' => $waitingBooking->id,
                'user_id' => $user2->id,
                'channel' => 'whatsapp',
                'recipient' => $user2->whatsapp_number,
                'trigger_type' => 'booking_confirmed',
                'message' => "Anda masuk Waiting List LT1-9 (PUT PNJ Lantai 1) untuk slot {$waitStart->format('H:i')} - {$waitEnd->format('H:i')}.",
                'sent_at' => $now->copy()->subMinutes(10),
            ]);
        }

        $lt2MejaS1 = FacilityUnit::where('institution_id', $putPnj->id)->where('code', 'LT2-S1')->first();

        if ($lt2MejaS1 && ! Booking::where('facility_unit_id', $lt2MejaS1->id)->whereIn('status', ['active', 'waiting'])->exists()) {
            $activeStart2 = $now->copy()->subMinutes(20);
            $activeEnd2 = $now->copy()->addMinutes(70);

            $activeBooking2 = Booking::create([
                'institution_id' => $putPnj->id,
                'facility_unit_id' => $lt2MejaS1->id,
                'user_id' => $user4->id,
                'group_name' => 'Nadia Putri & Tim PNJ',
                'people_count' => 2,
                'starts_at' => $activeStart2,
                'ends_at' => $activeEnd2,
                'status' => 'active',
            ]);

            NotificationLog::create([
                'booking_id' => $activeBooking2->id,
                'user_id' => $user4->id,
                'channel' => 'whatsapp',
                'recipient' => $user4->whatsapp_number,
                'trigger_type' => 'booking_confirmed',
                'message' => "Booking LT2-S1 (Meja Standar S1 — Lantai 2 PUT PNJ) dikonfirmasi untuk pukul {$activeStart2->format('H:i')} - {$activeEnd2->format('H:i')}.",
                'sent_at' => $activeStart2,
            ]);
        }

        if (DeveloperAuditLog::count() === 0) {
            DeveloperAuditLog::record(
                $primaryDev->id,
                'SYSTEM_SEEDED',
                'AntreKita Beta Test',
                'Inisialisasi 4 akun Developer.Ai dan 2 instansi resmi (PUT1PNJ26 dengan denah 2 lantai, LBR1UI26).'
            );
        }
    }

    protected function seedAdminAndInstitution(User $primaryDev, $now, array $inst): array
    {
        InstitutionWhitelist::updateOrCreate(
            ['email' => $inst['email']],
            [
                'institution_name' => $inst['name'],
                'status' => 'approved',
                'approved_by' => $primaryDev->id,
                'approved_at' => $now,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => $inst['email']],
            [
                'name' => $inst['admin_name'],
                'username' => $inst['username'],
                'affiliation' => $inst['name'],
                'whatsapp_number' => $inst['wa'],
                'role' => 'admin',
                'registration_code' => $inst['reg_code'],
                'institution_label' => $inst['name'],
                'purpose' => $inst['purpose'],
                'is_verified_admin' => true,
                'is_frozen' => false,
                'password' => Hash::make('password'),
            ]
        );

        $institution = Institution::updateOrCreate(
            ['slug' => $inst['slug']],
            [
                'admin_id' => $admin->id,
                'name' => $inst['name'],
                'registration_code' => $inst['reg_code'],
                'city' => $inst['city'],
                'building_type' => $inst['building_type'],
                'address' => $inst['address'],
                'description' => $inst['description'],
                'purpose' => $inst['purpose'],
                'is_active' => true,
                'map_cols' => $inst['map_cols'],
                'map_rows' => $inst['map_rows'],
                'has_custom_floorplan' => $inst['has_custom_floorplan'],
            ]
        );

        return ['admin' => $admin, 'institution' => $institution];
    }

    protected function seedPutPnjUnits(Institution $institution): void
    {
        $order = 1;
        $cols = max(3, $institution->map_cols);

        $upsertUnit = function (array $data) use ($institution, &$order, $cols) {
            $gridIndex = $order - 1;
            $posX = ($gridIndex % $cols) + 1;
            $posY = intdiv($gridIndex, $cols) + 1;

            FacilityUnit::updateOrCreate(
                ['institution_id' => $institution->id, 'code' => $data['code']],
                [
                    'name' => $data['name'],
                    'display_label' => $data['display_label'],
                    'zone' => $data['zone'],
                    'floor' => $data['floor'],
                    'seat_type' => $data['seat_type'],
                    'capacity' => $data['capacity'],
                    'max_duration_minutes' => $data['max_duration_minutes'] ?? 180,
                    'is_active' => true,
                    'sort_order' => $order++,
                    'pos_x' => $posX,
                    'pos_y' => $posY,
                    'map_x' => $data['map_x'],
                    'map_y' => $data['map_y'],
                    'map_w' => $data['map_w'],
                    'map_h' => $data['map_h'],
                ]
            );
        };

        // ============================================================
        // LANTAI 1 (FIRST FLOOR) — 45 Units
        // ============================================================
        $upsertUnit([
            'code' => 'LT1-PODCAST',
            'name' => 'Ruang Podcast',
            'display_label' => 'Ruang Podcast',
            'zone' => 'group_room',
            'floor' => 1,
            'seat_type' => 'ROOM',
            'capacity' => 4,
            'map_x' => 2.5,
            'map_y' => 5.5,
            'map_w' => 19.5,
            'map_h' => 23.5,
        ]);

        $upsertUnit([
            'code' => 'LT1-L1',
            'name' => 'Meja Leseh L1 (Lantai 1)',
            'display_label' => 'L1',
            'zone' => 'leseh_desk',
            'floor' => 1,
            'seat_type' => 'L',
            'capacity' => 2,
            'map_x' => 4.2,
            'map_y' => 32.5,
            'map_w' => 7.2,
            'map_h' => 7.5,
        ]);

        $upsertUnit([
            'code' => 'LT1-L2',
            'name' => 'Meja Leseh L2 (Lantai 1)',
            'display_label' => 'L2',
            'zone' => 'leseh_desk',
            'floor' => 1,
            'seat_type' => 'L',
            'capacity' => 2,
            'map_x' => 12.2,
            'map_y' => 32.5,
            'map_w' => 7.2,
            'map_h' => 7.5,
        ]);

        $upsertUnit([
            'code' => 'LT1-SOFA',
            'name' => 'Area Sofa Baca',
            'display_label' => 'SOFA',
            'zone' => 'sofa_area',
            'floor' => 1,
            'seat_type' => 'SOFA',
            'capacity' => 3,
            'map_x' => 21.5,
            'map_y' => 33.5,
            'map_w' => 6.0,
            'map_h' => 17.5,
        ]);

        $upsertUnit([
            'code' => 'LT1-PRANCIS',
            'name' => 'Ruang Prancis',
            'display_label' => 'Ruang Prancis',
            'zone' => 'group_room',
            'floor' => 1,
            'seat_type' => 'ROOM',
            'capacity' => 10,
            'map_x' => 2.5,
            'map_y' => 53.0,
            'map_w' => 19.5,
            'map_h' => 30.0,
        ]);

        // 7 Clusters of 2x2 Standard Desks (1..28)
        $stdClusters = [
            ['nums' => [1, 2, 3, 4],     'x' => 35.0, 'y' => 17.5],
            ['nums' => [5, 6, 7, 8],     'x' => 51.0, 'y' => 17.5],
            ['nums' => [9, 10, 11, 12],  'x' => 43.0, 'y' => 38.0],
            ['nums' => [13, 14, 15, 16], 'x' => 83.2, 'y' => 16.5],
            ['nums' => [17, 18, 19, 20], 'x' => 83.2, 'y' => 36.5],
            ['nums' => [21, 22, 23, 24], 'x' => 60.0, 'y' => 55.0],
            ['nums' => [25, 26, 27, 28], 'x' => 73.0, 'y' => 55.0],
        ];

        foreach ($stdClusters as $cluster) {
            $offsets = [
                [0.0, 0.0],
                [4.8, 0.0],
                [0.0, 7.5],
                [4.8, 7.5],
            ];
            foreach ($cluster['nums'] as $idx => $num) {
                $upsertUnit([
                    'code' => "LT1-{$num}",
                    'name' => "Meja Standar {$num} (Lantai 1)",
                    'display_label' => (string) $num,
                    'zone' => 'standard_desk',
                    'floor' => 1,
                    'seat_type' => 'S',
                    'capacity' => 2,
                    'map_x' => $cluster['x'] + $offsets[$idx][0],
                    'map_y' => $cluster['y'] + $offsets[$idx][1],
                    'map_w' => 4.6,
                    'map_h' => 7.2,
                ]);
            }
        }

        // 3 Clusters of 2x2 High Tables (H1..H12)
        $highClusters = [
            ['nums' => [1, 2, 3, 4],   'x' => 63.2, 'y' => 72.8],
            ['nums' => [5, 6, 7, 8],   'x' => 74.5, 'y' => 72.8],
            ['nums' => [9, 10, 11, 12], 'x' => 85.6, 'y' => 72.8],
        ];

        foreach ($highClusters as $cluster) {
            $offsets = [
                [0.0, 0.0],
                [3.6, 0.0],
                [0.0, 7.5],
                [3.6, 7.5],
            ];
            foreach ($cluster['nums'] as $idx => $num) {
                $upsertUnit([
                    'code' => "LT1-H{$num}",
                    'name' => "Meja & Kursi Tinggi H{$num} (Lantai 1)",
                    'display_label' => "H{$num}",
                    'zone' => 'high_desk',
                    'floor' => 1,
                    'seat_type' => 'H',
                    'capacity' => 1,
                    'map_x' => $cluster['x'] + $offsets[$idx][0],
                    'map_y' => $cluster['y'] + $offsets[$idx][1],
                    'map_w' => 3.4,
                    'map_h' => 7.2,
                ]);
            }
        }

        // ============================================================
        // LANTAI 2 (SECOND FLOOR) — 62 Units
        // ============================================================
        $leftRooms = [
            ['code' => 'LT2-PUSTAKA',    'name' => 'Sudut Pustaka',   'cap' => 6, 'y' => 2.2,  'h' => 21.5],
            ['code' => 'LT2-INTERAKTIF', 'name' => 'Zona Interaktif', 'cap' => 6, 'y' => 24.8, 'h' => 18.0],
            ['code' => 'LT2-SINERGI',    'name' => 'Ruang Sinergi',   'cap' => 8, 'y' => 43.8, 'h' => 18.0],
            ['code' => 'LT2-LAYAR',      'name' => 'Ruang Layar',     'cap' => 8, 'y' => 62.8, 'h' => 15.5],
            ['code' => 'LT2-PNJCORNER',  'name' => 'PNJ Corner',      'cap' => 6, 'y' => 79.3, 'h' => 17.2],
        ];

        foreach ($leftRooms as $room) {
            $upsertUnit([
                'code' => $room['code'],
                'name' => $room['name'],
                'display_label' => $room['name'],
                'zone' => 'group_room',
                'floor' => 2,
                'seat_type' => 'ROOM',
                'capacity' => $room['cap'],
                'map_x' => 1.5,
                'map_y' => $room['y'],
                'map_w' => 8.2,
                'map_h' => $room['h'],
            ]);
        }

        // Ruang Fokus Top Row (1..8)
        for ($i = 1; $i <= 8; $i++) {
            $upsertUnit([
                'code' => "LT2-F{$i}",
                'name' => "Meja Ruang Fokus {$i}",
                'display_label' => (string) $i,
                'zone' => 'focus_desk',
                'floor' => 2,
                'seat_type' => 'F',
                'capacity' => 1,
                'map_x' => 13.8 + (($i - 1) * 3.35),
                'map_y' => 5.2,
                'map_w' => 3.0,
                'map_h' => 6.5,
            ]);
        }

        // Ruang Fokus Bottom Row (9..14)
        for ($i = 9; $i <= 14; $i++) {
            $upsertUnit([
                'code' => "LT2-F{$i}",
                'name' => "Meja Ruang Fokus {$i}",
                'display_label' => (string) $i,
                'zone' => 'focus_desk',
                'floor' => 2,
                'seat_type' => 'F',
                'capacity' => 1,
                'map_x' => 13.8 + (($i - 9) * 3.9),
                'map_y' => 18.5,
                'map_w' => 3.5,
                'map_h' => 6.5,
            ]);
        }

        // L1 directly under Ruang Fokus
        $upsertUnit([
            'code' => 'LT2-L1',
            'name' => 'Meja Leseh L1 (Lantai 2)',
            'display_label' => 'L1',
            'zone' => 'leseh_desk',
            'floor' => 2,
            'seat_type' => 'L',
            'capacity' => 2,
            'map_x' => 22.4,
            'map_y' => 27.8,
            'map_w' => 4.0,
            'map_h' => 6.8,
        ]);

        // Above stairs Standard 2x2: S1, S3 (top row), S2, S4 (bottom row)
        $aboveStairsStd = [
            ['num' => 1, 'x' => 51.0, 'y' => 10.5],
            ['num' => 3, 'x' => 56.0, 'y' => 10.5],
            ['num' => 2, 'x' => 51.0, 'y' => 18.2],
            ['num' => 4, 'x' => 56.0, 'y' => 18.2],
        ];
        foreach ($aboveStairsStd as $std) {
            $upsertUnit([
                'code' => "LT2-S{$std['num']}",
                'name' => "Meja Standar S{$std['num']} (Lantai 2)",
                'display_label' => "S{$std['num']}",
                'zone' => 'standard_desk',
                'floor' => 2,
                'seat_type' => 'S',
                'capacity' => 2,
                'map_x' => $std['x'],
                'map_y' => $std['y'],
                'map_w' => 4.6,
                'map_h' => 7.2,
            ]);
        }

        // Left of stairs Leseh (L2..L13)
        $leftLeseh = [
            ['num' => 2,  'x' => 10.2, 'y' => 45.2, 'h' => 6.6],
            ['num' => 3,  'x' => 14.2, 'y' => 45.2, 'h' => 6.6],
            ['num' => 4,  'x' => 23.8, 'y' => 45.2, 'h' => 6.6],
            ['num' => 5,  'x' => 27.8, 'y' => 45.2, 'h' => 6.6],
            ['num' => 6,  'x' => 10.2, 'y' => 59.0, 'h' => 6.6],
            ['num' => 7,  'x' => 14.2, 'y' => 59.0, 'h' => 6.6],
            ['num' => 8,  'x' => 23.8, 'y' => 59.0, 'h' => 6.6],
            ['num' => 9,  'x' => 27.8, 'y' => 59.0, 'h' => 6.6],
            ['num' => 10, 'x' => 15.0, 'y' => 80.0, 'h' => 6.5],
            ['num' => 11, 'x' => 15.0, 'y' => 86.8, 'h' => 6.5],
            ['num' => 12, 'x' => 25.6, 'y' => 80.0, 'h' => 6.5],
            ['num' => 13, 'x' => 25.6, 'y' => 86.8, 'h' => 6.5],
        ];
        foreach ($leftLeseh as $l) {
            $upsertUnit([
                'code' => "LT2-L{$l['num']}",
                'name' => "Meja Leseh L{$l['num']} (Lantai 2)",
                'display_label' => "L{$l['num']}",
                'zone' => 'leseh_desk',
                'floor' => 2,
                'seat_type' => 'L',
                'capacity' => 2,
                'map_x' => $l['x'],
                'map_y' => $l['y'],
                'map_w' => 3.8,
                'map_h' => $l['h'],
            ]);
        }

        // Beside left wall of stairs High: H1, H2
        $leftWallHigh = [
            ['num' => 1, 'x' => 34.4, 'y' => 38.5],
            ['num' => 2, 'x' => 34.4, 'y' => 51.2],
        ];
        foreach ($leftWallHigh as $h) {
            $upsertUnit([
                'code' => "LT2-H{$h['num']}",
                'name' => "Meja & Kursi Tinggi H{$h['num']} (Lantai 2)",
                'display_label' => "H{$h['num']}",
                'zone' => 'high_desk',
                'floor' => 2,
                'seat_type' => 'H',
                'capacity' => 1,
                'map_x' => $h['x'],
                'map_y' => $h['y'],
                'map_w' => 3.8,
                'map_h' => 7.5,
            ]);
        }

        // Below stairs High: H14..H19
        $belowStairsHigh = [
            ['num' => 14, 'x' => 32.8, 'y' => 68.8],
            ['num' => 15, 'x' => 36.8, 'y' => 68.8],
            ['num' => 16, 'x' => 43.0, 'y' => 68.8],
            ['num' => 17, 'x' => 47.0, 'y' => 68.8],
            ['num' => 18, 'x' => 54.4, 'y' => 68.8],
            ['num' => 19, 'x' => 58.4, 'y' => 68.8],
        ];
        foreach ($belowStairsHigh as $h) {
            $upsertUnit([
                'code' => "LT2-H{$h['num']}",
                'name' => "Meja & Kursi Tinggi H{$h['num']} (Lantai 2)",
                'display_label' => "H{$h['num']}",
                'zone' => 'high_desk',
                'floor' => 2,
                'seat_type' => 'H',
                'capacity' => 1,
                'map_x' => $h['x'],
                'map_y' => $h['y'],
                'map_w' => 3.8,
                'map_h' => 6.6,
            ]);
        }

        // Right of stairs High: H3..H6
        $rightStairsHigh = [
            ['num' => 3, 'x' => 61.5, 'y' => 38.2],
            ['num' => 4, 'x' => 65.5, 'y' => 38.2],
            ['num' => 5, 'x' => 61.5, 'y' => 55.2],
            ['num' => 6, 'x' => 65.5, 'y' => 55.2],
        ];
        foreach ($rightStairsHigh as $h) {
            $upsertUnit([
                'code' => "LT2-H{$h['num']}",
                'name' => "Meja & Kursi Tinggi H{$h['num']} (Lantai 2)",
                'display_label' => "H{$h['num']}",
                'zone' => 'high_desk',
                'floor' => 2,
                'seat_type' => 'H',
                'capacity' => 1,
                'map_x' => $h['x'],
                'map_y' => $h['y'],
                'map_w' => 3.8,
                'map_h' => 6.8,
            ]);
        }

        // Right of stairs Standard 2x2: S5..S8
        $rightStairsStd = [
            ['num' => 5, 'x' => 72.8, 'y' => 38.0],
            ['num' => 6, 'x' => 77.2, 'y' => 38.0],
            ['num' => 7, 'x' => 72.8, 'y' => 45.0],
            ['num' => 8, 'x' => 77.2, 'y' => 45.0],
        ];
        foreach ($rightStairsStd as $s) {
            $upsertUnit([
                'code' => "LT2-S{$s['num']}",
                'name' => "Meja Standar S{$s['num']} (Lantai 2)",
                'display_label' => "S{$s['num']}",
                'zone' => 'standard_desk',
                'floor' => 2,
                'seat_type' => 'S',
                'capacity' => 2,
                'map_x' => $s['x'],
                'map_y' => $s['y'],
                'map_w' => 4.2,
                'map_h' => 6.8,
            ]);
        }

        // Right & Bottom-Right Leseh: L20..L29
        $rightLeseh = [
            ['num' => 20, 'x' => 71.2, 'y' => 76.2],
            ['num' => 21, 'x' => 71.2, 'y' => 83.2],
            ['num' => 22, 'x' => 82.4, 'y' => 75.2],
            ['num' => 23, 'x' => 82.4, 'y' => 82.2],
            ['num' => 24, 'x' => 84.4, 'y' => 53.0],
            ['num' => 25, 'x' => 84.4, 'y' => 60.0],
            ['num' => 26, 'x' => 93.2, 'y' => 58.0],
            ['num' => 27, 'x' => 93.2, 'y' => 65.0],
            ['num' => 28, 'x' => 93.2, 'y' => 76.8],
            ['num' => 29, 'x' => 93.2, 'y' => 83.8],
        ];
        foreach ($rightLeseh as $l) {
            $upsertUnit([
                'code' => "LT2-L{$l['num']}",
                'name' => "Meja Leseh L{$l['num']} (Lantai 2)",
                'display_label' => "L{$l['num']}",
                'zone' => 'leseh_desk',
                'floor' => 2,
                'seat_type' => 'L',
                'capacity' => 2,
                'map_x' => $l['x'],
                'map_y' => $l['y'],
                'map_w' => 4.0,
                'map_h' => 6.8,
            ]);
        }
    }

    protected function seedStandardGridUnits(Institution $institution, int $groups, int $indoor, int $outdoor): void
    {
        $order = 1;
        for ($i = 1; $i <= $groups; $i++) {
            $code = 'Ruang' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            FacilityUnit::updateOrCreate(
                ['institution_id' => $institution->id, 'code' => $code],
                [
                    'name' => "Ruang Diskusi Kelompok {$i}",
                    'display_label' => $code,
                    'zone' => 'group_room',
                    'floor' => 1,
                    'seat_type' => 'ROOM',
                    'capacity' => 6,
                    'max_duration_minutes' => 180,
                    'is_active' => true,
                    'sort_order' => $order++,
                    'pos_x' => $i,
                    'pos_y' => 1,
                ]
            );
        }

        for ($i = 1; $i <= $indoor; $i++) {
            $code = 'Meja' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            FacilityUnit::updateOrCreate(
                ['institution_id' => $institution->id, 'code' => $code],
                [
                    'name' => "Meja Pribadi Indoor {$i}",
                    'display_label' => $code,
                    'zone' => 'indoor_desk',
                    'floor' => 1,
                    'seat_type' => 'S',
                    'capacity' => 1,
                    'max_duration_minutes' => 180,
                    'is_active' => true,
                    'sort_order' => $order++,
                    'pos_x' => $i,
                    'pos_y' => 2,
                ]
            );
        }

        for ($i = 1; $i <= $outdoor; $i++) {
            $num = $indoor + $i;
            $code = 'Meja' . str_pad((string) $num, 2, '0', STR_PAD_LEFT);
            FacilityUnit::updateOrCreate(
                ['institution_id' => $institution->id, 'code' => $code],
                [
                    'name' => "Meja Pribadi Outdoor {$i}",
                    'display_label' => $code,
                    'zone' => 'outdoor_desk',
                    'floor' => 1,
                    'seat_type' => 'S',
                    'capacity' => 1,
                    'max_duration_minutes' => 180,
                    'is_active' => true,
                    'sort_order' => $order++,
                    'pos_x' => $i,
                    'pos_y' => 3,
                ]
            );
        }
    }
}
