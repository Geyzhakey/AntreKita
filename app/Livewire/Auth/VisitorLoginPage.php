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

#[Layout('components.layouts.app', ['title' => 'Sign In Visitor — AntreKita'])]
class VisitorLoginPage extends Component
{
    public string $identifier = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Masukkan Nama Pengguna, Email, atau Nomor Telepon Anda.',
        ]);

        $this->ensureIsNotRateLimited();

        $raw = trim($this->identifier);
        $lower = strtolower($raw);
        $normalizedPhone = User::normalizeWhatsapp($raw);

        $user = User::query()
            ->where('role', 'visitor')
            ->where(function ($q) use ($raw, $lower, $normalizedPhone) {
                $q->where('username', $lower)
                    ->orWhere('username', $raw)
                    ->orWhere('email', $lower);

                if ($normalizedPhone !== '') {
                    $q->orWhere('whatsapp_number', $normalizedPhone);
                }
            })
            ->first();

        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => 'user atau kata sandi tidak sesuai.',
            ]);
        }

        if ($user->is_frozen) {
            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda sedang dibekukan sementara' . ($user->frozen_reason ? ": {$user->frozen_reason}" : '.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->redirectIntended(route('visitor.institutions'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->identifier) . '|' . request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.visitor-login-page');
    }
}
