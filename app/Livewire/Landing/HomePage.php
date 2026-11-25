<?php

namespace App\Livewire\Landing;

use App\Models\Booking;
use App\Models\FacilityUnit;
use App\Models\Institution;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'AntreKita — Smart Spatial Queue & Multi-Facility Platform'])]
class HomePage extends Component
{
    public function render()
    {
        $institutions = Institution::withCount('facilityUnits')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalUnits = FacilityUnit::where('is_active', true)->count();
        $occupiedUnits = Booking::where('status', 'active')->count();
        $availableUnits = max(0, $totalUnits - $occupiedUnits);

        return view('livewire.landing.home-page', [
            'institutions' => $institutions,
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'availableUnits' => $availableUnits,
        ]);
    }
}
