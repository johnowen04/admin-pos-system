<div>
    <div class="position-relative">
        <form class="d-flex w-100" wire:submit.prevent>
            <div class="input-group w-100">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input x-data="{}" x-init="$nextTick(() => $el.focus())"
                    x-on:livewire:update="setTimeout(() => $el.focus(), 10)" wire:model.live.debounce.300ms="search"
                    class="form-control" placeholder="Search outlets by name..." aria-label="Search" type="search">
            </div>
        </form>

        @if (count($selectedOutlets) > 0)
            <div class="mt-2 d-flex flex-wrap gap-2">
                @foreach ($selectedOutlets as $outlet)
                    <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2 border">
                        <span class="me-2">{{ $outlet['name'] }}</span>
                        <button type="button" class="btn-close btn-sm" wire:click="removeOutlet({{ $outlet['id'] }})"
                            aria-label="Remove outlet">
                        </button>
                        <input type="hidden" name="outlets[]" value="{{ $outlet['id'] }}">
                    </div>
                @endforeach

                @if (count($selectedOutlets) > 1)
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearSelectedOutlets">
                        Clear All
                    </button>
                @endif
            </div>
        @endif

        @if ($showResults)
            <div class="dropdown-menu w-100 show" style="margin-top: 2px; max-height: 350px; overflow-y: auto;">
                @forelse($searchResults as $outlet)
                    <div class="px-3 py-2 border-bottom outlet-item" wire:key="outlet-{{ $outlet['id'] }}"
                        wire:click="selectOutlet({{ $outlet['id'] }})"
                        style="cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor=''">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="fw-medium">{{ $outlet['name'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-3 text-center text-muted">
                        <p class="mb-0">No outlet found matching "{{ $search }}"</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
