<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_register_page_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_users_can_register(): void
    {
        Livewire::test(\App\Livewire\Auth\Register::class)
            ->set('name', 'Budi')
            ->set('email', 'budi@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
        ]);
    }

    public function test_users_can_login(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create();

        Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }
}
