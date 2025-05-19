<div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
        <ul class="nav nav-secondary">
            @foreach ($filteredMenuItems as $item)
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
        </ul>
    </div>
</div>