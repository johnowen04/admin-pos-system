<div>
    <div class="position-relative">
        <form class="d-flex w-100" wire:submit.prevent>
            <div class="input-group w-100">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input x-data="{}" x-init="$nextTick(() => $el.focus())"
                    x-on:livewire:update="setTimeout(() => $el.focus(), 10)" wire:model.live.debounce.300ms="search"
                    class="form-control" placeholder="Search category by name..." aria-label="Search" type="search">
            </div>
        </form>

        @if ($showResults)
            <div class="w-100 show form-control" style="margin-top: 8px; max-height: 350px; overflow-y: auto;">
                @forelse($searchResults as $category)
                    <div class="form-check" wire:key="category-{{ $category['id'] }}">
                        <input class="form-check-input" type="checkbox" id="category-{{ $category['id'] }}"
                            wire:model.live="selectedCategories" value="{{ $category['id'] }}"
                            name="categories[]" style="width: 1.25rem; height: 1.25rem; margin-top: 0.2rem;">
                        <label class="form-check-label" for="category-{{ $category['id'] }}">
                            {{ $category['name'] }}
                        </label>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        @if ($search)
                            <p class="mb-0">No categories found matching "{{ $search }}"</p>
                        @else
                            <p class="mb-0">No categories available</p>
                        @endif
                    </div>
                @endforelse
            </div>
        @endif

        @if (count($searchResults) > 0)
            <div class="d-flex justify-content-between mt-2">
                <span class="text-muted">{{ count($selectedCategories) }} categories selected</span>
            </div>
        @endif
    </div>
</div>
