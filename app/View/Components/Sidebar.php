<?php

namespace App\View\Components;

use App\Services\AccessControlService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menuItems;
    public $filteredMenuItems;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($menuItems = [])
    {
        $this->menuItems = $menuItems;
        $this->filteredMenuItems = $this->filterMenuItems($menuItems);
    }

    /**
     * Filter menu items based on permissions
     */
    protected function filterMenuItems($items)
    {
        // Return empty array if user is not logged in or no employee relation
        if (!Auth::check() || !Auth::user()->employee) {
            return [];
        }

        // Create access control service
        $accessControl = new AccessControlService(Auth::user()->employee);
        $filteredItems = [];
        
        foreach ($items as $item) {
            $permissionSlug = $this->getPermissionSlug($item['name']);
            
            if (isset($item['children'])) {
                $filteredChildren = [];
                
                foreach ($item['children'] as $child) {
                    $childPermissionSlug = $this->getPermissionSlug($child['name']);
                    
                    if ($accessControl->hasPermission($childPermissionSlug)) {
                        $filteredChildren[] = $child;
                    }
                }
                
                // Show parent menu if it has visible children or parent itself is accessible
                if (!empty($filteredChildren) || $accessControl->hasPermission($permissionSlug)) {
                    $item['children'] = $filteredChildren;
                    $filteredItems[] = $item;
                }
            } else if ($accessControl->hasPermission($permissionSlug)) {
                $filteredItems[] = $item;
            }
        }
        
        return $filteredItems;
    }
    
    /**
     * Get permission slug from menu name
     */
    protected function getPermissionSlug($name)
    {
        $slug = strtolower(str_replace('&', '', $name));
        $slug = str_replace(' ', '-', $slug);
        
        // Handle special cases that need mapping
        switch ($slug) {
            case 'role-permission': return 'role.view';
            case 'pos': return 'pos.view';
            case 'base-unit': return 'bu.view';
            default: return $slug . '.view';
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.sidebar', [
            'menuItems' => $this->filteredMenuItems
        ]);
    }
}