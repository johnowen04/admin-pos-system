<?php

namespace App\Livewire;

use App\Models\Permission;
use Livewire\Component;

class PositionPermissionTable extends Component
{
    public $search = "";
    public $position = null;
    public $searchResults = [];
    public $showResults = true;
    public $selectedPermission = [];
    public $selectedPermissions = [];
    public $originalPermissions = [];
    public $groupBy = 'feature';

    public function mount($position = null)
    {
        $this->position = $position;

        if ($this->position) {
            $positionPermissions = Permission::with(['feature', 'operation'])
                ->whereHas('position', function ($query) {
                    $query->where('position_id', $this->position->id);
                })->get();
            
            $this->selectedPermission = $positionPermissions->pluck('id')->toArray();
            
            foreach ($positionPermissions as $permission) {
                $this->selectedPermissions[$permission->feature->id][$permission->operation->id] = true;
                $this->originalPermissions[$permission->feature->id][$permission->operation->id] = true;
            }
        }

        $this->loadPermission();
    }

    public function updatedSearch()
    {
        $this->loadPermission();
    }

    public function setGrouping($groupBy)
    {
        $this->groupBy = $groupBy;
        $this->loadPermission();
    }

    public function togglePermission($permissionId, $featureId, $operationId, $checked)
    {
        if ($checked) {
            $this->selectedPermissions[$featureId][$operationId] = true;
            if (!in_array($permissionId, $this->selectedPermission)) {
                $this->selectedPermission[] = $permissionId;
            }
        } else {
            unset($this->selectedPermissions[$featureId][$operationId]);
            $this->selectedPermission = array_diff($this->selectedPermission, [$permissionId]);
        }
    }

    public function toggleAll($checked)
    {
        if ($checked) {
            foreach ($this->searchResults as $group) {
                foreach ($group['permissions'] as $permission) {
                    $featureId = $permission['feature_id'];
                    $operationId = $permission['operation_id'];
                    $permissionId = $permission['permission_id'];
                    
                    $this->selectedPermissions[$featureId][$operationId] = true;
                    if (!in_array($permissionId, $this->selectedPermission)) {
                        $this->selectedPermission[] = $permissionId;
                    }
                }
            }
        } else {
            $this->selectedPermissions = [];
            $this->selectedPermission = [];
        }
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
                        'feature_id' => $permission->feature->id,
                        'operation_id' => $permission->operation->id,
                        'permission_id' => $permission->id,
                        'name' => $permission->operation->name,
                        'slug' => $permission->slug
                    ];
                })->values()->all()
            ];
        })->values()->all();
    }
    
    private function applyGroupByOperation($permissions)
    {
        return $permissions->groupBy(function ($permission) {
            return $permission->operation->id;
        })->map(function ($operationPermissions) {
            $firstPermission = $operationPermissions->first();
            return [
                'id' => $firstPermission->operation->id,
                'name' => $firstPermission->operation->name,
                'type' => 'operation',
                'permissions' => $operationPermissions->map(function ($permission) {
                    return [
                        'feature_id' => $permission->feature->id,
                        'operation_id' => $permission->operation->id,
                        'permission_id' => $permission->id,
                        'name' => $permission->feature->name,
                        'slug' => $permission->slug
                    ];
                })->values()->all()
            ];
        })->values()->all();
    }

    private function loadPermission()
    {
        $query = Permission::with(['feature', 'operation'])->where('is_super_user_only', 0);

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function($query) use ($search) {
                $query->whereHas('feature', function ($q) use ($search) {
                    $q->where('name', 'LIKE', $search);
                })
                ->orWhereHas('operation', function ($q) use ($search) {
                    $q->where('name', 'LIKE', $search);
                })
                ->orWhere('slug', 'LIKE', $search);
            });
        }

        if (!empty($this->selectedPermission)) {
            $query->orderByRaw('FIELD(id, ' . implode(',', $this->selectedPermission) . ') DESC')
                ->orderBy('id');
        } else {
            $query->orderBy('id');
        }

        $permissions = $query->get();

        if ($this->groupBy === 'feature') {
            $this->searchResults = $this->applyGroupByFeature($permissions);
        } else {
            $this->searchResults = $this->applyGroupByOperation($permissions);
        }

        $this->showResults = count($this->searchResults) > 0;
    }

    public function render()
    {
        return view('livewire.position-permission-table');
    }
}