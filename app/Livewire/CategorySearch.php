<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategorySearch extends Component
{
    public $search = "";
    public $departmentId = null;
    public $searchResults = [];
    public $showResults = true;
    public $selectedCategories = [];

    public function mount($departmentId = null)
    {
        $this->departmentId = $departmentId;

        if ($this->departmentId) {
            $this->selectedCategories = Category::where('department_id', $this->departmentId)
                ->pluck('id')
                ->toArray();
        }

        $this->loadCategories();
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    private function loadCategories()
    {
        $query = Category::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedCategories)) {
            $query->orderByRaw('FIELD(id, ' . implode(',', $this->selectedCategories) . ') DESC')
                ->orderBy('name');
        } else {
            $query->orderBy('name');
        }

        $this->searchResults = $query->limit(20)->get()->toArray();

        $this->showResults = count($this->searchResults) > 0;
    }

    public function render()
    {
        return view('livewire.category-search');
    }
}
