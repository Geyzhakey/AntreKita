<?php

use App\Models\Institution;
use App\Models\InstitutionWhitelist;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sync/Update PUT Politeknik Negeri Jakarta Admin
        $pnjAdmin = User::whereRaw('UPPER(registration_code) = ?', ['PUT1PNJ26'])->first();
        if ($pnjAdmin) {
            $pnjAdmin->update([
                'name' => 'Admin PUT Politeknik Negeri Jakarta',
                'institution_label' => 'Politeknik Negeri Jakarta',
                'password' => Hash::make('Admin#1234'),
            ]);
        }

        // 2. Provision Universitas Sumatera Utara (USU)
        $usuAdmin = User::whereRaw('UPPER(registration_code) = ?', ['USU1MEDAN26'])->first();
        if (! $usuAdmin) {
            $usuAdmin = User::create([
                'name' => 'rotigoreng',
                'username' => 'rotigoreng',
                'affiliation' => 'Universitas Sumatera Utara',
                'email' => 'rotigoreng@usu.ac.id',
                'whatsapp_number' => '+6281222822531',
                'role' => 'admin',
                'registration_code' => 'USU1MEDAN26',
                'institution_label' => 'Universitas Sumatera Utara',
                'purpose' => 'ingin buat akun ea. (Perpustakaan Universitas Sumatera Utara)',
                'is_verified_admin' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('pasword123'),
            ]);
        } else {
            $usuAdmin->update([
                'password' => Hash::make('pasword123'),
            ]);
        }

        $usuInst = Institution::whereRaw('UPPER(registration_code) = ?', ['USU1MEDAN26'])->first();
        if (! $usuInst) {
            Institution::create([
                'admin_id' => $usuAdmin->id,
                'name' => 'Universitas Sumatera Utara',
                'slug' => 'universitas-sumatera-utara',
                'registration_code' => 'USU1MEDAN26',
                'city' => 'Medan',
                'building_type' => 'Perpustakaan',
                'address' => 'Medan, Sumatera Utara',
                'description' => 'Perpustakaan Universitas Sumatera Utara (USU)',
                'purpose' => 'ingin buat akun ea.',
                'is_active' => true,
                'map_cols' => 8,
                'map_rows' => 6,
                'has_custom_floorplan' => false,
            ]);
        }

        InstitutionWhitelist::updateOrCreate(
            ['email' => 'rotigoreng@usu.ac.id'],
            [
                'institution_name' => 'Universitas Sumatera Utara',
                'status' => 'approved',
                'approved_by' => 1,
                'approved_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to keep records in down
    }
};
