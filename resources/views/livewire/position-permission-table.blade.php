<div>
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold mb-3">Permissions for: {{ old('name', $position->name ?? '') }}</h5>
        <div class="btn-group" role="group">
            <button type="button" class="btn {{ $groupBy === 'feature' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="setGrouping('feature')">
                <i class="fas fa-th-large me-1"></i> Group by Feature
            </button>
            <button type="button" class="btn {{ $groupBy === 'operation' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="setGrouping('operation')">
                <i class="fas fa-tasks me-1"></i> Group by Operation
            </button>
        </div>
    </div>

    <div class="d-flex mb-3">
        <form class="w-100" wire:submit.prevent>
            <div class="input-group w-100">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input x-data="{}" x-init="$nextTick(() => $el.focus())"
                    x-on:livewire:update="setTimeout(() => $el.focus(), 10)" wire:model.live.debounce.300ms="search"
                    class="form-control" placeholder="Search permission by feature, operation, or slug..."
                    aria-label="Search" type="search">
            </div>
        </form>
    </div>

    @if ($showResults)
        <div class="col-md-12 mb-3">
            @if (empty($searchResults))
                <div class="alert alert-info text-center py-4">
                    <div class="mb-3">
                        <i class="fa fa-info-circle fa-3x text-info"></i>
                    </div>
                    <h5>No Permissions Available</h5>
                    <p>
                        There are no permissions configured in the system yet.
                        <br>Please create some features and operations first.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('permission.index') }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-tasks me-1"></i> Manage Permission
                        </a>
                        <a href="{{ route('feature.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-tasks me-1"></i> Manage Features
                        </a>
                    </div>
                </div>
            @else
                <div x-data="{ checkAll: false }" class="table-wrapper"
                    style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="text-center" style="max-width: 150px;">
                                    {{ $groupBy === 'feature' ? 'Feature' : 'Operation' }}
                                </th>
                                <th class="text-center" style="max-width: 150px;">
                                    {{ $groupBy === 'feature' ? 'Operation' : 'Feature' }}
                                </th>
                                <th class="text-center" style="min-width: 90px;">Before</th>
                                <th class="text-center" style="min-width: 90px;">
                                    <div>After</div>
                                    <input type="checkbox" class="form-check-input" x-model="checkAll"
                                        @change="$wire.toggleAll($event.target.checked);
                                        $refs.permissionTable.querySelectorAll('.operation-checkbox').forEach(el => el.checked = $event.target.checked)">
                                </th>
                            </tr>
                        </thead>
                        <tbody x-ref="permissionTable">
                            @foreach ($searchResults as $group)
                                @php $firstRow = true; @endphp
                                @foreach ($group['permissions'] as $permission)
                                    <tr>
                                        @if ($firstRow)
                                            <td class="text-center align-middle"
                                                rowspan="{{ count($group['permissions']) }}">
                                                {{ ucfirst($group['name']) }}
                                            </td>
                                            @php $firstRow = false; @endphp
                                        @endif
                                        <td>{{ ucfirst($permission['name']) }}</td>
                                        <td class="text-center">
                                            @if (isset($originalPermissions[$permission['feature_id']][$permission['operation_id']]))
                                                <i class="fa fa-check text-success"></i>
                                            @else
                                                <i class="fa fa-times text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <input id="allowedPermission_{{ $permission['permission_id'] }}"
                                                class="operation-checkbox form-check-input" type="checkbox"
                                                name="permissions[]" value="{{ $permission['permission_id'] }}"
                                                {{ isset($selectedPermissions[$permission['feature_id']][$permission['operation_id']]) ? 'checked' : '' }}
                                                wire:change="togglePermission({{ $permission['permission_id'] }}, {{ $permission['feature_id'] }}, {{ $permission['operation_id'] }}, $event.target.checked)"
                                                @change="document.querySelectorAll('.operation-checkbox').length === document.querySelectorAll('.operation-checkbox:checked').length ? checkAll = true : checkAll = false">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
