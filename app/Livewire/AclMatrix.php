<?php

namespace App\Livewire;

use App\Models\Permission;
use App\Models\Position;
use Livewire\Component;

class AclMatrix extends Component
{
    public $permissions = [];
    public $positions = [];
    public $showResults = true;
    public $selectedPermission = [];
    public $selectedPermissions = [];
    public $isEditing = false;
    public $openDetails = [];

    public function mount()
    {
        $this->positions = Position::all();
        foreach ($this->positions as $position) {
            $positionPermissions = Permission::with(['feature', 'operation'])
                ->whereHas('position', function ($query) use ($position) {
                    $query->where('position_id', $position->id);
                })->get();

            $this->selectedPermission[$position->id] = $positionPermissions->pluck('id')->toArray();

            foreach ($positionPermissions as $permission) {
                $this->selectedPermissions[$position->id][] = $permission->id;
            }
        }
        $this->loadPermission();
    }

    public function toggleDetails($featureId)
    {
        if (in_array($featureId, $this->openDetails)) {
            $this->openDetails = array_diff($this->openDetails, [$featureId]);
        } else {
            $this->openDetails[] = $featureId;
        }
    }

    private function loadPermission()
    {
        $query = Permission::with(['feature', 'operation'])->where('is_super_user_only', 0);

        $this->permissions = $this->applyGroupByFeature($query->get());
        $this->showResults = count($this->permissions) > 0;
    }

    public function enableEditing()
    {
        $this->isEditing = true;
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->loadPermission();
    }

    private function applyGroupByFeature($permissions)
    {
        return $permissions->groupBy(function ($permission) {
            return $permission->feature->id;
        })->map(function ($featurePermissions) {
            $firstPermission = $featurePermissions->first();
            return [
                'id' => $firstPermission->feature->id,
                'name' => $firstPermission->feature->name,
                'type' => 'feature',
                'permissions' => $featurePermissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'slug' => $permission->slug,
                        'name' => $permission->operation->name,
                        'feature_id' => $permission->feature->id,
                        'operation_id' => $permission->operation->id,
                    ];
                })->values()->all()
            ];
        })->values()->all();
    }

    public function render()
    {
        return view('livewire.acl-matrix');
    }
}
