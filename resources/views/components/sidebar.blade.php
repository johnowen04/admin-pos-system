<div class="sidebar sidebar-style-2" data-background-color="dark">
    <x-sidebar-logo logo="assets/img/kaiadmin/logo_light.svg" backgroundColor="dark" />

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Full Outlet Selector (visible when sidebar expanded) -->
                @if ($outlets->isEmpty() || (Auth::user()->employee && Auth::user()->employee->outlets->isEmpty()))
                    <div class="nav-item outlet-selector">
                        <div class="card p-3">
                            <div class="alert alert-info text-center py-4">
                                <div class="mb-3">
                                    <i class="fa fa-store-alt fa-2x text-info"></i>
                                </div>
                                <h6 class="mb-1">No Outlets</h6>
                                @can('outlet.create')
                                    <p class="small mb-1">
                                        Please create outlets first.
                                    </p>

                                    <div class="mt-2">
                                        <a href="{{ route('outlet.index') }}" class="btn btn-xs btn-primary py-1 px-2"
                                            style="background-color: #4e73df !important; border-color: #4e73df !important; color: white !important; opacity: 1 !important;">
                                            <i class="fa fa-store" style="color: white !important;"></i> Manage
                                        </a>
                                        <a href="{{ route('outlet.create') }}"
                                            class="btn btn-xs btn-outline-primary py-1 px-2"
                                            style="background-color: transparent !important; border-color: #4e73df !important; color: black !important; opacity: 1 !important;">
                                            <i class="fa fa-plus" style="color: black !important;"></i> Add
                                        </a>
                                    </div>
                                @else
                                    <p class="small mb-1">
                                        Ask administrator to create or assign outlet.
                                    </p>
                                @endcan
                            </div>
                        </div>
                    </div>
                @elseif (
                    !Auth::user()->employee ||
                        (Auth::user()->employee &&
                            Auth::user()->employee->position->level->value > \App\Enums\PositionLevel::MANAGER->value))
                    <div class="nav-item outlet-selector">
                        <div class="card p-3">
                            <div class="card-header">
                                <h6 class="fw-bold">Current Outlet</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend d-flex">
                                            <span class="input-group-text bg-transparent border-0">
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                        </div>
                                        <select id="outletSelector" class="form-select form-control border-0"
                                            onchange="selectOutletFromDropdown(this)">
                                            <option value="all">
                                                All Outlet
                                            </option>
                                            <optgroup label="──────────">
                                                @foreach ($outlets as $outlet)
                                                    <option value="{{ $outlet->id }}"
                                                        {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                                        {{ $outlet->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @foreach ($filteredMenuItems as $section)
                    <li class="nav-section">
                        <h4 class="text-section">{{ $section['name'] }}</h4>
                    </li>
                    @foreach ($section['children'] as $item)
                        <li class="nav-item {{ $item['active'] ? 'active' : '' }}">
                            @if (isset($item['children']) && count($item['children']) > 0)
                                <a data-bs-toggle="collapse" href="#{{ $item['link'] }}"
                                    class="{{ $item['active'] ? '' : 'collapsed' }}"
                                    aria-expanded="{{ $item['active'] ? 'true' : 'false' }}">
                                @else
                                    <a href="{{ route($item['route']) }}">
                            @endif
                            <i class="{{ $item['icon'] }}"></i>
                            <p>{{ $item['name'] }}</p>
                            @if (isset($item['children']) && count($item['children']) > 0)
                                <span class="caret"></span>
                            @endif
                            </a>
                            @if (isset($item['children']) && count($item['children']) > 0)
                                <div class="collapse {{ $item['active'] ? 'show' : '' }}" id="{{ $item['link'] }}">
                                    <ul class="nav nav-collapse">
                                        @foreach ($item['children'] as $child)
                                            <li class="{{ $child['active'] ? 'active' : '' }}">
                                                <a href="{{ route($child['route']) }}">
                                                    <span class="sub-item">{{ $child['name'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
</div>

<style>
    /* Ensure dropdowns appear on top */
    .dropdown-menu {
        z-index: 1030;
    }

    /* Style for outlet selector in expanded view */
    .outlet-selector .card-header {
        padding: 0.5rem 0;
        background: transparent;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .wrapper.sidebar_minimize.sidebar_minimize_hover .outlet-selector {
        display: block !important;
    }

    .wrapper.sidebar_minimize .outlet-selector {
        display: none !important;
    }

    /* Show it as block on small screens (e.g., below 768px) */
    @media (max-width: 991px) {
        .wrapper.sidebar_minimize .outlet-selector {
            display: block !important;
        }
    }
</style>

@push('scripts')
    <script>
        function selectOutletFromDropdown(selectElement) {
            const outletId = selectElement.value;
            const outletName = selectElement.options[selectElement.selectedIndex].text;

            $.ajax({
                url: "{{ route('select-outlet.select') }}",
                method: "POST",
                dataType: "json",
                data: {
                    id: outletId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Failed to select outlet.');
                    console.error("Error selecting outlet:", error);
                }
            });
        }
    </script>
@endpush
