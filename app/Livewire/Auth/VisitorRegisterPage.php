<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Sign Up Visitor — AntreKita'])]
class VisitorRegisterPage extends Component
{
    public string $username = '';

    public string $affiliation = '';

    public string $email = '';

    public string $whatsapp_number = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $cleanUsername = strtolower(trim($this->username));
        $cleanEmail = strtolower(trim($this->email));
        $normalizedPhone = User::normalizeWhatsapp($this->whatsapp_number);

        $this->username = $cleanUsername;
        $this->email = $cleanEmail;
        $this->whatsapp_number = $normalizedPhone;

        $this->validate([
            'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash', Rule::unique(User::class, 'username')],
            'affiliation' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'whatsapp_number' => ['required', 'string', 'regex:/^\+62[0-9]{8,14}$/', Rule::unique(User::class, 'whatsapp_number')],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ], [
            'username.alpha_dash' => 'Nama Pengguna hanya boleh huruf, angka, garis bawah (_), atau tanda hubung (-).',
            'username.unique' => 'Nama Pengguna ini sudah dipakai.',
            'whatsapp_number.regex' => 'Nomor Telepon/WhatsApp harus aktif dengan format +628... atau 08... (8-14 digit).',
            'whatsapp_number.unique' => 'Nomor Telepon ini sudah terdaftar.',
        ]);

        $user = User::create([
            'name' => $cleanUsername,
            'username' => $cleanUsername,
            'affiliation' => trim($this->affiliation),
            'email' => $cleanEmail,
            'whatsapp_number' => $normalizedPhone,
            'role' => 'visitor',
            'is_verified_admin' => false,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('visitor.institutions'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.visitor-register-page');
    }
}
