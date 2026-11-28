<?php

namespace App\Livewire\Developer;

use App\Models\Booking;
use App\Models\DeveloperAuditLog;
use App\Models\Institution;
use App\Models\InstitutionWhitelist;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Developer Command Center — AntreKita'])]
class ControlCenterPage extends Component
{
    public string $search = '';

    // Provision New Admin Form
    public string $pic_name = '';

    public string $pic_email = '';

    public string $pic_whatsapp = '';

    public string $institution_name = '';

    public string $registration_code = '';

    public string $building_type = 'Perpustakaan Kampus';

    public string $city = '';

    public string $address = '';

    public string $purpose = '';

    public string $password = '';

    // Inspect Modal
    public ?int $inspectAdminId = null;

    // Freeze Modal
    public ?int $freezeAdminId = null;

    public string $freezeReason = '';

    // 5x Confirmation Delete Modal
    public ?int $deleteAdminId = null;

    public int $deleteStep = 0;

    public string $deleteConfirmCode = '';

    public string $deleteError = '';

    // Platform Authority Settings
    public string $developer_whatsapp = '';

    public string $global_announcement = '';

    public bool $booking_maintenance_mode = false;

    public function mount(): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);

        $this->developer_whatsapp = SystemSetting::getValue('developer_whatsapp', '6281200000000') ?? '6281200000000';
        $this->global_announcement = SystemSetting::getValue('global_announcement', '') ?? '';
        $this->booking_maintenance_mode = SystemSetting::getValue('booking_maintenance_mode', '0') === '1';
    }

    public function createAdminAccount(): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);

        $this->registration_code = strtoupper(trim($this->registration_code));
        $this->pic_email = strtolower(trim($this->pic_email));
        $waNormalized = User::normalizeWhatsapp($this->pic_whatsapp);

        $this->validate([
            'pic_name' => ['required', 'string', 'max:150'],
            'pic_email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'institution_name' => ['required', 'string', 'max:150'],
            'registration_code' => ['required', 'string', 'min:4', 'max:40', 'alpha_dash', Rule::unique(User::class, 'registration_code'), Rule::unique(Institution::class, 'registration_code')],
            'building_type' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($waNormalized) {
            $admin = User::create([
                'name' => trim($this->pic_name),
                'username' => Str::slug($this->registration_code, '_'),
                'affiliation' => trim($this->institution_name),
                'email' => $this->pic_email,
                'whatsapp_number' => $waNormalized ?: null,
                'role' => 'admin',
                'registration_code' => $this->registration_code,
                'institution_label' => trim($this->institution_name),
                'purpose' => trim($this->purpose),
                'is_verified_admin' => true,
                'is_frozen' => false,
                'password' => Hash::make($this->password),
            ]);

            $baseSlug = Str::slug($this->institution_name);
            $slug = Institution::where('slug', $baseSlug)->exists()
                ? $baseSlug . '-' . strtolower($this->registration_code)
                : $baseSlug;

            Institution::create([
                'admin_id' => $admin->id,
                'name' => trim($this->institution_name),
                'slug' => $slug,
                'registration_code' => $this->registration_code,
                'city' => trim($this->city),
                'building_type' => trim($this->building_type),
                'address' => trim($this->address),
                'description' => trim($this->purpose),
                'purpose' => trim($this->purpose),
                'is_active' => true,
                'map_cols' => 6,
                'map_rows' => 4,
            ]);

            InstitutionWhitelist::updateOrCreate(
                ['email' => $this->pic_email],
                [
                    'institution_name' => trim($this->institution_name),
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]
            );

            DeveloperAuditLog::record(
                Auth::id(),
                'CREATE_ADMIN',
                "{$this->institution_name} ({$this->registration_code})",
                "Mendaftarkan akun admin {$this->pic_email} untuk {$this->building_type}."
            );
        });

        $code = $this->registration_code;
        $this->reset(['pic_name', 'pic_email', 'pic_whatsapp', 'institution_name', 'registration_code', 'city', 'address', 'purpose', 'password']);
        $this->building_type = 'Perpustakaan Kampus';

        $this->dispatch('wa-notify', [
            'title' => 'Akun Instansi Diterbitkan',
            'recipient' => $code,
            'message' => "Akun Administrator dan Kode Daftar {$code} aktif dan siap digunakan.",
        ]);
    }

    public function openInspect(int $adminId): void
    {
        $this->inspectAdminId = $adminId;
    }

    public function closeInspect(): void
    {
        $this->inspectAdminId = null;
    }

    public function openFreezeModal(int $adminId): void
    {
        $this->freezeAdminId = $adminId;
        $this->freezeReason = '';
    }

    public function confirmFreeze(): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);
        if (! $this->freezeAdminId) {
            return;
        }

        $admin = User::where('role', 'admin')->findOrFail($this->freezeAdminId);
        $reason = trim($this->freezeReason) ?: 'Pembekuan sementara oleh otoritas Developer.';

        $admin->update([
            'is_frozen' => true,
            'frozen_reason' => $reason,
            'frozen_at' => now(),
        ]);

        if ($admin->institution) {
            $admin->institution->update(['is_active' => false]);
        }

        DeveloperAuditLog::record(
            Auth::id(),
            'FREEZE_ADMIN',
            "{$admin->institution_label} ({$admin->registration_code})",
            "Dibekukan sementara. Alasan: {$reason}"
        );

        $this->freezeAdminId = null;
        $this->freezeReason = '';
    }

    public function unfreezeAdmin(int $adminId): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);

        $admin = User::where('role', 'admin')->findOrFail($adminId);
        $admin->update([
            'is_frozen' => false,
            'frozen_reason' => null,
            'frozen_at' => null,
        ]);

        if ($admin->institution) {
            $admin->institution->update(['is_active' => true]);
        }

        DeveloperAuditLog::record(
            Auth::id(),
            'UNFREEZE_ADMIN',
            "{$admin->institution_label} ({$admin->registration_code})",
            'Pembekuan akun dicabut dan fasilitas diaktifkan kembali.'
        );
    }

    public function startDelete(int $adminId): void
    {
        $this->deleteAdminId = $adminId;
        $this->deleteStep = 1;
        $this->deleteConfirmCode = '';
        $this->deleteError = '';
    }

    public function advanceDeleteStep(): void
    {
        $this->deleteError = '';
        if ($this->deleteStep >= 1 && $this->deleteStep < 5) {
            $this->deleteStep++;
        }
    }

    public function cancelDelete(): void
    {
        $this->deleteAdminId = null;
        $this->deleteStep = 0;
        $this->deleteConfirmCode = '';
        $this->deleteError = '';
    }

    public function executeDelete(): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);
        if (! $this->deleteAdminId || $this->deleteStep !== 5) {
            return;
        }

        $admin = User::with('institution')->where('role', 'admin')->findOrFail($this->deleteAdminId);
        $expectedCode = strtoupper((string) $admin->registration_code);

        if (strtoupper(trim($this->deleteConfirmCode)) !== $expectedCode) {
            $this->deleteError = "Kode konfirmasi tidak cocok. Ketik persis: {$expectedCode}";

            return;
        }

        $label = "{$admin->institution_label} ({$expectedCode})";

        DB::transaction(function () use ($admin, $label) {
            InstitutionWhitelist::where('email', $admin->email)->delete();
            $admin->delete();

            DeveloperAuditLog::record(
                Auth::id(),
                'DELETE_ADMIN_5X',
                $label,
                'Akun Administrator beserta seluruh data instansi, unit spasial, dan riwayat booking dihapus permanen setelah konfirmasi 5 tahap.'
            );
        });

        $this->cancelDelete();

        $this->dispatch('wa-notify', [
            'title' => 'Akun Dihapus Permanen',
            'message' => "{$label} telah dihapus setelah konfirmasi 5x.",
        ]);
    }

    public function savePlatformSettings(): void
    {
        abort_unless(Auth::user()?->isDeveloper(), 403);

        $wa = preg_replace('/[^\d]/', '', trim($this->developer_whatsapp)) ?: '6281200000000';
        SystemSetting::setValue('developer_whatsapp', $wa);
        SystemSetting::setValue('global_announcement', trim($this->global_announcement));
        SystemSetting::setValue('booking_maintenance_mode', $this->booking_maintenance_mode ? '1' : '0');

        $this->developer_whatsapp = $wa;

        DeveloperAuditLog::record(
            Auth::id(),
            'PLATFORM_CONTROL',
            'Global Settings',
            'WA Developer: +' . $wa . ' | Maintenance: ' . ($this->booking_maintenance_mode ? 'ON' : 'OFF')
        );

        $this->dispatch('wa-notify', [
            'title' => 'Kontrol Platform Diperbarui',
            'message' => 'Pengaturan otoritas Developer telah diterapkan ke seluruh situs.',
        ]);
    }

    public function render()
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->with(['institution.facilityUnits'])
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('institution_label', 'like', $term)
                        ->orWhere('registration_code', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $inspectAdmin = $this->inspectAdminId
            ? User::with(['institution.facilityUnits.activeBooking.user', 'institution.facilityUnits.waitingBookings'])->find($this->inspectAdminId)
            : null;

        $deleteTarget = $this->deleteAdminId
            ? User::with('institution')->find($this->deleteAdminId)
            : null;

        $freezeTarget = $this->freezeAdminId
            ? User::find($this->freezeAdminId)
            : null;

        return view('livewire.developer.control-center-page', [
            'admins' => $admins,
            'developers' => User::where('role', 'developer')->orderBy('registration_code')->get(),
            'inspectAdmin' => $inspectAdmin,
            'deleteTarget' => $deleteTarget,
            'freezeTarget' => $freezeTarget,
            'totalVisitors' => User::where('role', 'visitor')->count(),
            'activeBookingsCount' => Booking::whereIn('status', ['active', 'waiting'])->count(),
            'auditLogs' => DeveloperAuditLog::with('developer')->latest()->limit(12)->get(),
        ]);
    }
}
