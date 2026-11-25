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

    public bool $showRequestModal = false;

    public string $req_name = '';

    public string $req_institution = '';

    public string $req_desired_password = '';

    public string $req_purpose = '';

    public function login(): void
    {
        $this->validate([
            'institution_name' => ['required', 'string'],
            'registration_code' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'institution_name.required' => 'Masukkan Nama Instansi Anda.',
            'registration_code.required' => 'Masukkan Kode Daftar gedung/instansi (misal: LBR1ITB26).',
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

    public function fillDemo(string $inst, string $code, string $pass = 'password'): void
    {
        $this->institution_name = $inst;
        $this->registration_code = $code;
        $this->password = $pass;
    }

    public function getWhatsappUrlProperty(): string
    {
        $text = "Halo Tim AntreKita,\nSaya ingin mendaftarkan Akun Administrator Instansi:\n"
            . "• Nama Pendaftar: " . ($this->req_name ?: '-') . "\n"
            . "• Nama Instansi: " . ($this->req_institution ?: '-') . "\n"
            . "• Password yang Diinginkan: " . ($this->req_desired_password ?: '-') . "\n"
            . "• Tujuan Permintaan Kebutuhan: " . ($this->req_purpose ?: '-') . "\n\n"
            . "Mohon pembuatan Kode Daftar resmi untuk gedung kami. Terima kasih.";

        return 'https://wa.me//081222822531?text=' . rawurlencode($text);
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
