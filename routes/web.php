<?php

use App\Livewire\Admin\FacilitySetupPage;
use App\Livewire\Admin\LiveMonitorPage;
use App\Livewire\Auth\AdminLoginPage;
use App\Livewire\Auth\AuthPortalPage;
use App\Livewire\Auth\VisitorLoginPage;
use App\Livewire\Auth\VisitorRegisterPage;
use App\Livewire\Developer\ControlCenterPage;
use App\Livewire\Landing\HomePage;
use App\Livewire\Visitor\ActiveSessionPage;
use App\Livewire\Visitor\InstitutionPickerPage;
use App\Livewire\Visitor\SpatialMapPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', AuthPortalPage::class)->name('login');
    Route::get('/login/visitor', VisitorLoginPage::class)->name('login.visitor');
    Route::get('/register/visitor', VisitorRegisterPage::class)->name('register.visitor');
    Route::get('/login/admin', AdminLoginPage::class)->name('login.admin');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/developer/console', ControlCenterPage::class)->name('developer.console');
    Route::redirect('/developer/whitelist', '/developer/console')->name('developer.whitelist');

    Route::get('/admin/setup', FacilitySetupPage::class)->name('admin.setup');
    Route::get('/admin/monitor', LiveMonitorPage::class)->name('admin.monitor');

    Route::get('/explore', InstitutionPickerPage::class)->name('visitor.institutions');
    Route::get('/facility/{institution:slug}', SpatialMapPage::class)->name('visitor.map');
    Route::get('/session', ActiveSessionPage::class)->name('visitor.session');
});
