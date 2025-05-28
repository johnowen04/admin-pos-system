<?php

namespace App\View\Components;

use App\Services\AccessControlService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $filteredMenuItems;
    protected AccessControlService $accessControl;

    public function __construct(AccessControlService $accessControl)
    {
        $this->accessControl = $accessControl;
        $this->filteredMenuItems = $this->filterMenuItems($this->menuStructure());
    }

    protected function isActive(array|string $patterns): bool
    {
        foreach ((array) $patterns as $pattern) {
            if (Route::is($pattern)) {
                return true;
            }
        }
        return false;
    }

    protected function menuStructure(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'children' => [
                    [
                        'name' => 'Dashboard',
                        'link' => 'dashboard',
                        'icon' => 'fas fa-home',
                        'route' => 'dashboard',
                        'active' => $this->isActive('dashboard'),
                    ],
                ],
            ],
            [
                'name' => 'Super User',
                'children' => [
                    [
                        'name' => 'Permission',
                        'link' => 'permission',
                        'icon' => 'fas fa-user-shield',
                        'route' => 'permission.index',
                        'active' => $this->isActive(['feature.*', 'operation.*', 'permission.*', 'acl.*']),
                        'children' => [
                            ['name' => 'Feature', 'route' => 'feature.index', 'active' => $this->isActive('feature')],
                            ['name' => 'Operation', 'route' => 'operation.index', 'active' => $this->isActive('operation')],
                            ['name' => 'Permission', 'route' => 'permission.index', 'active' => $this->isActive('permission')],
                            ['name' => 'ACL Matrix', 'route' => 'acl.index', 'active' => $this->isActive('acl')],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Admin',
                'children' => [
                    [
                        'name' => 'Employee',
                        'link' => 'employee',
                        'icon' => 'fas fa-user',
                        'route' => 'employee.index',
                        'active' => $this->isActive(['employee.*', 'position.*']),
                        'children' => [
                            ['name' => 'Employee', 'route' => 'employee.index', 'active' => $this->isActive('employee')],
                            ['name' => 'Position', 'route' => 'position.index', 'active' => $this->isActive('position')],
                        ],
                    ],
                    [
                        'name' => 'Outlet',
                        'link' => 'outlet',
                        'icon' => 'fas fa-building',
                        'route' => 'outlet.index',
                        'active' => $this->isActive('outlet.*'),
                    ],
                    [
                        'name' => 'Unit',
                        'link' => 'unit',
                        'icon' => 'fas fa-ruler-horizontal',
                        'route' => 'unit.index',
                        'active' => $this->isActive(['unit.*', 'bu.*']),
                        'children' => [
                            ['name' => 'Base Unit', 'route' => 'bu.index', 'active' => $this->isActive('bu')],
                            ['name' => 'Unit', 'route' => 'unit.index', 'active' => $this->isActive('unit')],
                        ],
                    ],
                    [
                        'name' => 'Product',
                        'link' => 'product',
                        'icon' => 'fas fa-box-open',
                        'route' => 'product.index',
                        'active' => $this->isActive(['product.*', 'category.*', 'department.*']),
                        'children' => [
                            ['name' => 'Department', 'route' => 'department.index', 'active' => $this->isActive('department')],
                            ['name' => 'Category', 'route' => 'category.index', 'active' => $this->isActive('category')],
                            ['name' => 'Product', 'route' => 'product.index', 'active' => $this->isActive('product')],
                        ],
                    ],
                    [
                        'name' => 'Inventory',
                        'link' => 'inventory',
                        'icon' => 'fas fa-boxes',
                        'route' => 'inventory.index',
                        'active' => $this->isActive(['inventory.*', 'purchase.*', 'sales.*']),
                        'children' => [
                            ['name' => 'Purchase', 'route' => 'purchase.index', 'active' => $this->isActive('purchase')],
                            ['name' => 'Sales', 'route' => 'sales.index', 'active' => $this->isActive('sales')],
                            ['name' => 'Inventory', 'route' => 'inventory.index', 'active' => $this->isActive('inventory')],

                        ],
                    ],
                    [
                        'name' => 'Reports',
                        'link' => 'reports',
                        'icon' => 'fas fa-clipboard-list',
                        'route' => '#',
                        'active' => $this->isActive(['reports.*']),
                        'children' => [
                            ['name' => 'Product Sales Report', 'route' => 'reports.sales.products', 'active' => $this->isActive('reports.sales.product')],
                            ['name' => 'Category Sales Report', 'route' => 'reports.sales.categories', 'active' => $this->isActive('reports.sales.category')],
                            ['name' => 'Department Sales Report', 'route' => 'reports.sales.departments', 'active' => $this->isActive('reports.sales.department')],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Point of Sales',
                'children' => [
                    [
                        'name' => 'POS',
                        'link' => 'pos',
                        'route' => 'pos.index',
                        'icon' => 'fas fa-shopping-cart',
                        'active' => $this->isActive('pos.*'),
                    ],
                ],
            ],
        ];
    }

    protected function getPermissionSlug(string $route): string
    {
        if (empty($route)) {
            return '';
        }

        $parts = explode('.', $route);
        return $parts[0] . '.view';
    }

    protected function filterMenuItems(array $items): array
    {
        if (!$this->accessControl->hasUser()) {
            return [];
        }

        if ($this->accessControl->isSuperUser()) {
            return $items;
        }

        $filteredItems = [];

        foreach ($items as $section) {
            $filteredSection = [];
            foreach ($section['children'] as $item) {
                $permissionSlug = $this->getPermissionSlug($item['route'] ?? '');
                $itemHasPermission = $this->accessControl->hasPermission($permissionSlug);

                $filteredChildren = [];
                if (!empty($item['children'])) {
                    $filteredChildren = array_filter($item['children'], function ($child) {
                        $childSlug = $this->getPermissionSlug($child['route'] ?? '');
                        return $this->accessControl->hasPermission($childSlug);
                    });
                }

                if (!empty($filteredChildren) || $itemHasPermission) {
                    $item['children'] = array_values($filteredChildren);
                    $filteredSection[] = $item;
                }
            }

            if (!empty($filteredSection)) {
                $section['children'] = array_values($filteredSection);
                $filteredItems[] = $section;
            }
        }

        return $filteredItems;
    }

    public function render()
    {
        $menuItems = $this->menuStructure();
        $this->filteredMenuItems = $this->filterMenuItems($menuItems);

        return view('components.sidebar', [
            'filteredMenuItems' => $this->filteredMenuItems,
        ]);
    }
}
