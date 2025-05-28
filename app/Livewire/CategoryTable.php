<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'up';
    public $page = 1;
    public $perPage = 10;
    public $selectedOutletId;
    public $filter = 'all';
    public $departmentFilter = '';

    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'page'];

    public function mount($selectedOutletId)
    {
        $this->selectedOutletId = $selectedOutletId;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'up' ? 'down' : 'up';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'up';
        }
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'sortField', 'sortDirection', 'page']);
        $this->filter = 'all';
        $this->departmentFilter = '';
    }

    public function render()
    {
        $query = Category::query();

        if ($this->selectedOutletId && $this->selectedOutletId !== 'all') {
            $query->whereHas('outlets', function ($q) {
                $q->where('outlet_id', $this->selectedOutletId);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->departmentFilter) {
            $query->where('department_id', $this->departmentFilter);
        }

        $this->applyFilters($query);

        $categories = $query->orderBy($this->sortField, $this->sortDirection == "up" ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.category-table', [
            'categories' => $categories,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    protected function applyFilters($query)
    {
        if ($this->filter === 'shown') {
            $query->where('is_shown', true);
        } elseif ($this->filter === 'not_shown') {
            $query->where('is_shown', false);
        }

        return $query;
    }
}
