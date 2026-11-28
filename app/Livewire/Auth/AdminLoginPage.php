<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Sign In Administrator — AntreKita'])]
class AdminLoginPage extends Component
{
    public string $institution_name = '';

    public string $registration_code = '';

    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'institution_name' => ['required', 'string'],
            'registration_code' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'institution_name.required' => 'Masukkan Nama Instansi Anda.',
            'registration_code.required' => 'Masukkan Kode Daftar gedung/instansi Anda.',
        ]);

        $this->ensureIsNotRateLimited();

        $instInput = strtolower(trim($this->institution_name));
        $codeInput = strtoupper(trim($this->registration_code));

        $user = User::query()
            ->whereIn('role', ['admin', 'developer'])
            ->whereRaw('UPPER(registration_code) = ?', [$codeInput])
            ->where(function ($q) use ($instInput) {
                $q->whereRaw('LOWER(institution_label) = ?', [$instInput])
                    ->orWhereHas('institution', fn ($iq) => $iq->whereRaw('LOWER(name) = ?', [$instInput]));
            })
            ->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'registration_code' => 'Kombinasi Nama Instansi, Kode Daftar, atau Password tidak cocok.',
            ]);
        }

        if ($user->is_frozen) {
            throw ValidationException::withMessages([
                'registration_code' => 'Akun Instansi dibekukan sementara' . ($user->frozen_reason ? ": {$user->frozen_reason}" : '.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Auth::login($user);
        session()->regenerate();

        $destination = $user->role === 'developer'
            ? route('developer.console')
            : route('admin.monitor');

        $this->redirectIntended($destination, navigate: true);
    }

    public function getWhatsappUrlProperty(): string
    {
        $rawNumber = \App\Models\SystemSetting::getValue('developer_whatsapp', '6281222822531') ?: '6281222822531';
        $digits = preg_replace('/[^\d]/', '', (string) $rawNumber);
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        $template = "Halo Tim AntreKita,\n"
            . "Saya ingin mendaftarkan Akun Administrator Instansi baru.\n\n"
            . "Silakan lengkapi format pendaftaran berikut:\n"
            . "• Nama Pendaftar: [Isi Nama Lengkap Anda]\n"
            . "• Nama Instansi / Gedung: [Isi Nama Instansi]\n"
            . "• Jenis Gedung: [Perpustakaan / Pusat Belajar / Co-Working]\n"
            . "• Kota & Alamat Lengkap: [Isi Lokasi Gedung]\n"
            . "• Password yang Diinginkan: [Isi Kata Sandi Akun]\n"
            . "• Tujuan Permintaan Kebutuhan: [Jelaskan Kebutuhan Pemetaan Ruangan/Meja]\n\n"
            . "Mohon penerbitan Kode Daftar resmi untuk gedung kami. Terima kasih.";

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($template);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'registration_code' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::upper($this->registration_code) . '|' . request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.admin-login-page');
    }
}
