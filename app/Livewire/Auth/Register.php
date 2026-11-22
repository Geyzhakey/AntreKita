<?php

namespace App\Livewire\Auth;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;


#[Layout('components.kerangka', ['title' => 'Login - AntreKita'])]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(CreateNewUser $creator): void
    {
        $user = $creator->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
