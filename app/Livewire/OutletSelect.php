<?php

namespace App\Livewire;

use App\Models\Outlet;
use Livewire\Component;

class OutletSelect extends Component
{
    public $search = '';
    public $searchResults = [];
    public $selectedOutlets = [];
    public $showResults = false;

    public function mount($selectedOutletIds = [])
    {
        if (!empty($selectedOutletIds)) {
            $outlets = Outlet::whereIn('id', $selectedOutletIds)->get();
            foreach ($outlets as $outlet) {
                $this->selectedOutlets[] = [
                    'id' => $outlet->id,
                    'name' => $outlet->name
                ];
            }
        }
    }

    public function updatedSearch()
    {
        if (empty($this->search) || strlen($this->search) < 2) {
            $this->searchResults = [];
            $this->showResults = false;
            return;
        }

        $this->searchResults = Outlet::where('name', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->get()
            ->map(function ($outlet) {
                return [
                    'id' => $outlet->id,
                    'name' => $outlet->name
                ];
            })
            ->toArray();

        $this->showResults = count($this->searchResults) > 0;
    }

    public function selectOutlet($outletId)
    {
        $outlet = collect($this->searchResults)->firstWhere('id', $outletId);
        
        if ($outlet && !collect($this->selectedOutlets)->contains('id', $outletId)) {
            $this->selectedOutlets[] = $outlet;
            $this->dispatch('outletsUpdated', ['outlets' => collect($this->selectedOutlets)->pluck('id')->toArray()]);
        }
        
        $this->search = '';
        $this->showResults = false;
    }

    public function removeOutlet($outletId)
    {
        $this->selectedOutlets = collect($this->selectedOutlets)
            ->reject(function ($outlet) use ($outletId) {
                return $outlet['id'] == $outletId;
            })
            ->values()
            ->toArray();
            
        $this->dispatch('outletsUpdated', ['outlets' => collect($this->selectedOutlets)->pluck('id')->toArray()]);
    }

    public function clearSelectedOutlets()
    {
        $this->selectedOutlets = [];
        $this->dispatch('outletsUpdated', ['outlets' => []]);
    }
    
    public function hideResults()
    {
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.outlet-select');
    }
}