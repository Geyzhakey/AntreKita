<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Portal Masuk — AntreKita'])]
class AuthPortalPage extends Component
{
    public function render()
    {
        return view('livewire.auth.auth-portal-page');
    }
}
