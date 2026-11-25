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

        // 2. Verified Institutions & Admins with Kode Daftar + 2D Coordinates
        $institutionsData = [
            [
                'email' => 'perpus@itb.ac.id',
                'username' => 'admin_itb',
                'admin_name' => 'Admin Perpustakaan ITB',
                'wa' => '+6281200001003',
                'reg_code' => 'LBR1ITB26',
                'name' => 'Perpustakaan Pusat ITB',
                'slug' => 'perpus-itb',
                'city' => 'Bandung',
                'building_type' => 'Perpustakaan Kampus',
                'address' => 'Jl. Ganesa No. 10, Coblong, Kota Bandung',
                'description' => 'Fasilitas ruang kolaborasi tim, meja belajar indoor tenang, dan area baca outdoor di lingkungan ITB.',
                'purpose' => 'Pemetaan lantai 1 & 2 gedung perpustakaan pusat ITB (Ruang diskusi + meja baca).',
                'groups' => 3,
                'indoor' => 5,
                'outdoor' => 4,
            ],
            [
                'email' => 'perpustakaanui1@gmail.ac.id',
                'username' => 'admin_ui',
                'admin_name' => 'Admin Perpustakaan UI',
                'wa' => '+6281200001001',
                'reg_code' => 'LBR1UI26',
                'name' => 'Perpustakaan Universitas Indonesia',
                'slug' => 'perpus-ui',
                'city' => 'Depok',
                'building_type' => 'Perpustakaan Kampus',
                'address' => 'Kampus UI Depok, Jawa Barat',
                'description' => 'Perpustakaan pusat (The Crystal of Knowledge) dengan ruang diskusi kelompok, meja baca indoor, dan area outdoor tepi danau.',
                'purpose' => 'Manajemen antrean ruang diskusi dan meja baca mahasiswa Universitas Indonesia.',
                'groups' => 3,
                'indoor' => 5,
                'outdoor' => 5,
            ],
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
                'description' => 'Pusat Layanan Terpadu & ruang belajar bersama mahasiswa Politeknik Negeri Jakarta.',
                'purpose' => 'Koordinasi meja kerja kelompok dan kursi baca mahasiswa Politeknik Negeri Jakarta.',
                'groups' => 2,
                'indoor' => 4,
                'outdoor' => 4,
            ],
        ];

        $seededInstitutions = [];

        foreach ($institutionsData as $inst) {
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
                    'map_cols' => 6,
                    'map_rows' => 4,
                ]
            );

            $order = 1;
            // Row 1: Group Rooms (pos_y = 1)
            for ($i = 1; $i <= $inst['groups']; $i++) {
                $code = 'Ruang' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                FacilityUnit::updateOrCreate(
                    ['institution_id' => $institution->id, 'code' => $code],
                    [
                        'name' => "Ruang Diskusi Kelompok {$i}",
                        'zone' => 'group_room',
                        'capacity' => 6,
                        'max_duration_minutes' => 180,
                        'is_active' => true,
                        'sort_order' => $order++,
                        'pos_x' => $i,
                        'pos_y' => 1,
                    ]
                );
            }

            // Row 2: Indoor Desks (pos_y = 2)
            for ($i = 1; $i <= $inst['indoor']; $i++) {
                $code = 'Meja' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                FacilityUnit::updateOrCreate(
                    ['institution_id' => $institution->id, 'code' => $code],
                    [
                        'name' => "Meja Pribadi Indoor {$i}",
                        'zone' => 'indoor_desk',
                        'capacity' => 1,
                        'max_duration_minutes' => 180,
                        'is_active' => true,
                        'sort_order' => $order++,
                        'pos_x' => $i,
                        'pos_y' => 2,
                    ]
                );
            }

            // Row 3: Outdoor Desks (pos_y = 3)
            for ($i = 1; $i <= $inst['outdoor']; $i++) {
                $num = $inst['indoor'] + $i;
                $code = 'Meja' . str_pad((string) $num, 2, '0', STR_PAD_LEFT);
                FacilityUnit::updateOrCreate(
                    ['institution_id' => $institution->id, 'code' => $code],
                    [
                        'name' => "Meja Pribadi Outdoor {$i}",
                        'zone' => 'outdoor_desk',
                        'capacity' => 1,
                        'max_duration_minutes' => 180,
                        'is_active' => true,
                        'sort_order' => $order++,
                        'pos_x' => $i,
                        'pos_y' => 3,
                    ]
                );
            }

            $seededInstitutions[$inst['slug']] = $institution;
        }

        // 3. Visitors with Nama Pengguna (username) + Instansi (affiliation)
        $user1 = User::updateOrCreate(
            ['email' => 'pengguna1@gmail.com'],
            [
                'name' => 'Dandy Aulia',
                'username' => 'dandy_aulia',
                'affiliation' => 'Institut Teknologi Bandung',
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

        // 4. Pre-seeded Scenario on Meja09 at Perpustakaan UI
        $perpusUi = $seededInstitutions['perpus-ui'];
        $meja09 = FacilityUnit::where('institution_id', $perpusUi->id)->where('code', 'Meja09')->first();

        if ($meja09 && ! Booking::where('facility_unit_id', $meja09->id)->whereIn('status', ['active', 'waiting'])->exists()) {
            $activeStart = $now->copy()->subMinutes(40);
            $activeEnd = $now->copy()->addMinutes(55);

            $activeBooking = Booking::create([
                'institution_id' => $perpusUi->id,
                'facility_unit_id' => $meja09->id,
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
                'message' => "Booking Meja09 dikonfirmasi untuk pukul {$activeStart->format('H:i')} - {$activeEnd->format('H:i')}.",
                'sent_at' => $activeStart,
            ]);

            $waitStart = $activeEnd->copy();
            $waitEnd = $waitStart->copy()->addMinutes(90);

            $waitingBooking = Booking::create([
                'institution_id' => $perpusUi->id,
                'facility_unit_id' => $meja09->id,
                'user_id' => $user2->id,
                'group_name' => 'Geyzha Keyriz (Individu)',
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
                'message' => "Anda masuk Waiting List Meja09 untuk slot {$waitStart->format('H:i')} - {$waitEnd->format('H:i')}.",
                'sent_at' => $now->copy()->subMinutes(10),
            ]);
        }

        if (DeveloperAuditLog::count() === 0) {
            DeveloperAuditLog::record(
                $primaryDev->id,
                'SYSTEM_SEEDED',
                'AntreKita Beta Test',
                'Inisialisasi 4 akun Developer.Ai dan 3 instansi resmi (LBR1ITB26, LBR1UI26, PUT1PNJ26).'
            );
        }
    }
}
