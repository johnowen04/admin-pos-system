<div class="card p-2">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h4 class="card-title">ACL Matrix</h4>
            <div class="ms-auto d-flex gap-2">
                @if (!$isEditing)
                    <button type="button" class="btn btn-round btn-primary" wire:click="enableEditing">
                        <i class="fas fa-pen me-2"></i> Edit
                    </button>
                @else
                    <button type="button" class="btn btn-round btn-secondary" wire:click="cancelEditing">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                    <button type="submit" class="btn btn-round btn-success me-2">
                        <i class="fas fa-save me-2"></i> Save
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="card-body" x-data="aclMatrix()">
        @if (empty($permissions) || $positions->isEmpty())
            <div class="empty-state text-center py-5">
                <div class="empty-state-icon">
                    <i class="fa fa-lock fa-3x text-muted"></i>
                </div>
                <h4 class="mt-4">No Permission Data Available</h4>
                <p class="text-muted">
                    There are no permissions configured in the system.
                    <br>Please create some features, operations, and positions first.
                </p>
                <div class="mt-3">
                    <a href="{{ route('position.index') }}" class="btn btn-primary me-2">
                        <i class="fa fa-user-shield me-1"></i> Manage Position
                    </a>
                    <a href="{{ route('feature.index') }}" class="btn btn-outline-primary">
                        <i class="fa fa-tasks me-1"></i> Manage Features
                    </a>
                </div>
            </div>
        @else
            @foreach ($permissions as $feature)
                <div class="mb-4">
                    <details class="border rounded shadow-sm p-3"
                        {{ in_array($feature['id'], $openDetails) ? 'open' : '' }}>
                        <summary class="fw-bold" wire:click.stop="toggleDetails({{ $feature['id'] }})">
                            {{ $feature['name'] }}
                        </summary>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Operation</th>
                                        @foreach ($positions as $position)
                                            <th class="text-center">{{ $position->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feature['permissions'] as $permission)
                                        <tr>
                                            <td class="text-center">
                                                {{ $permission['name'] }}
                                                <input type="checkbox" class="form-check-input ms-2 operation-checkbox"
                                                    data-feature="{{ $feature['name'] }}"
                                                    data-operation="{{ $permission['name'] }}"
                                                    x-on:change="toggleOperationRow($event, '{{ $feature['name'] }}', '{{ $permission['name'] }}')"
                                                    {{ $isEditing ? '' : 'disabled' }}>
                                            </td>
                                            @foreach ($positions as $position)
                                                <input type="hidden" name="permissions[{{ $position['id'] }}][]"
                                                    value="">
                                                <td class="text-center">
                                                    <input type="checkbox" class="form-check-input permission-checkbox"
                                                        name="permissions[{{ $position['id'] }}][]"
                                                        value="{{ $permission['id'] }}"
                                                        data-feature="{{ $feature['name'] }}"
                                                        data-operation="{{ $permission['name'] }}"
                                                        data-position="{{ $position['name'] }}"
                                                        x-on:change="togglePermission($event, '{{ $feature['name'] }}', '{{ $permission['name'] }}', '{{ $position['name'] }}')"
                                                        {{ in_array($permission['id'], $selectedPermission[$position['id']]) ? 'checked' : '' }}
                                                        {{ $isEditing ? '' : 'disabled' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
    function aclMatrix() {
        return {
            /**
             * Toggle all permission checkboxes in a row when an operation checkbox is changed
             */
            toggleOperationRow(event, feature, operation) {
                const isChecked = event.target.checked;
                document.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]`
                ).forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                if (operation === '*') {
                    document.querySelectorAll(`.operation-checkbox[data-feature="${feature}"]`)
                        .forEach(checkbox => {
                            if (checkbox.getAttribute('data-operation') !== '*') {
                                checkbox.checked = isChecked;

                                const opOperation = checkbox.getAttribute('data-operation');
                                document.querySelectorAll(
                                    `.permission-checkbox[data-feature="${feature}"][data-operation="${opOperation}"]`
                                ).forEach(permCheckbox => {
                                    permCheckbox.checked = isChecked;
                                });
                            }
                        });
                }
            },

            /**
             * Handle permission checkbox changes
             */
            togglePermission(event, feature, operation, position) {
                const isChecked = event.target.checked;
                if (operation === '*') {
                    document.querySelectorAll(
                        `.permission-checkbox[data-feature="${feature}"][data-position="${position}"]`
                    ).forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    document.querySelectorAll(`.operation-checkbox[data-feature="${feature}"]`)
                        .forEach(opCheckbox => {
                            const currOperation = opCheckbox.getAttribute('data-operation');
                            this.updateOperationCheckbox(feature, currOperation);
                        });
                } else {
                    this.updateOperationCheckbox(feature, operation);
                }
            },

            /**
             * Update operation checkbox state based on its permission checkboxes
             */
            updateOperationCheckbox(feature, operation) {
                const operationCheckbox = document.querySelector(
                    `.operation-checkbox[data-feature="${feature}"][data-operation="${operation}"]`);
                if (!operationCheckbox) return;
                const permissionCheckboxes = document.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]`);
                const allChecked = Array.from(permissionCheckboxes).every(checkbox => checkbox.checked);
                operationCheckbox.checked = allChecked;
            }
        }
    }
</script>
