<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;

// Halaman Register
Route::livewire('/', Register::class)
    ->name('register');

// Halaman Login
Route::livewire('/login', Login::class)
    ->name('login');

// Kalau user sudah login
Route::livewire('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');