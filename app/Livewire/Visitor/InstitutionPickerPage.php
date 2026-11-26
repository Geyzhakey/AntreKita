<?php

namespace App\Livewire\Visitor;

use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Pilih Fasilitas — AntreKita'])]
class InstitutionPickerPage extends Component
{
    public string $search = '';

    public function render()
    {
        $user = Auth::user();
        $activeBooking = $user?->activeBooking()->with(['facilityUnit', 'institution'])->first();

        $institutions = Institution::query()
            ->where('is_active', true)
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->with(['facilityUnits' => fn ($q) => $q->where('is_active', true)->with('activeBooking')])
            ->orderByDesc('has_custom_floorplan')
            ->orderBy('name')
            ->get();

        return view('livewire.visitor.institution-picker-page', [
            'institutions' => $institutions,
            'activeBooking' => $activeBooking,
        ]);
    }
}
