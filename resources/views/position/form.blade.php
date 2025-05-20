<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="positionName">Role Name</label>
                        <input type="text" class="form-control" id="positionName" name="name"
                            placeholder="Ex: Administrator, Owner, Manager etc"
                            value="{{ old('name', $position->name ?? '') }}" required />
                    </div>
                </div>

                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="defaultSelect">Level</label>
                        <select class="form-select form-control" id="defaultSelect" name="level">
                            <option value="" disabled selected>Select level</option>
                            @foreach ($positionLevels as $level)
                                <option value="{{ $level->value }}"
                                    {{ old('level', $position->level->value ?? '') == $level->value ? 'selected' : '' }}>
                                    {{ $level->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('level'))
                            <div class="text-danger">
                                {{ $errors->first('level') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="row-md-4 mt-4 ms-2">
                <div class="col-md-12 mb-3">
                    <h5 class="fw-bold">Permissions for: {{ old('name', $position->name ?? '') }}</h5>

                    @if (empty($permissions))
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
                                <a href="{{ route('feature.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-tasks me-1"></i> Manage Features
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Feature</th>
                                        <th class="text-center">Operation</th>
                                        <th class="text-center">Before</th>
                                        <th class="text-center">
                                            <div>After</div>
                                            <input type="checkbox" class="form-check-input ms-2" id="checkboxSelectAll"
                                                onclick="toggleCheckboxes(this.checked)">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $feature)
                                        @php $firstRow = true; @endphp
                                        @foreach ($feature['operations'] as $operation)
                                            <tr>
                                                @if ($firstRow)
                                                    <td class="text-center"
                                                        rowspan="{{ count($feature['operations']) }}">
                                                        {{ ucfirst($feature['name']) }}
                                                    </td>
                                                    @php $firstRow = false; @endphp
                                                @endif
                                                <td>{{ ucfirst($operation['name']) }}</td>
                                                <td class="text-center">
                                                    @if (isset($currentPermissions[$feature['id']][$operation['id']]) &&
                                                            $currentPermissions[$feature['id']][$operation['id']]
                                                    )
                                                        <i class="fa fa-check text-success"></i>
                                                    @else
                                                        <i class="fa fa-times text-danger"></i>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <!-- Editable checkbox for "Allowed?" -->
                                                    <input id="allowedPermission_{{ $operation['permission_id'] }}"
                                                        class="operation-checkbox" type="checkbox"
                                                        data-feature="{{ $feature['id'] }}"
                                                        data-operation="{{ $operation['id'] }}"
                                                        name="permissions[{{ $operation['permission_id'] }}]"
                                                        value="1"
                                                        {{ isset($currentPermissions[$feature['id']][$operation['id']]) && $currentPermissions[$feature['id']][$operation['id']] ? 'checked' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkboxes for the "Allowed?" column
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="permissions"]');
            const selectAllCheckbox = document.getElementById('checkboxSelectAll');

            // Function to update the "Select All" checkbox state
            const updateSelectAllCheckboxState = function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            };

            // Function to toggle all checkboxes
            window.toggleCheckboxes = function(checked) {
                checkboxes.forEach(cb => cb.checked = checked);
            };

            // Add event listeners to all operation checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Update the "Select All" checkbox state
                    updateSelectAllCheckboxState();

                    // Handle feature-specific behavior for "*" operations
                    const feature = this.dataset.feature;
                    const operation = this.dataset.operation;

                    // If the "*" operation is checked/unchecked
                    if (operation === '1') {
                        const featureCheckboxes = document.querySelectorAll(
                            `.operation-checkbox[data-feature="${feature}"]`);
                        featureCheckboxes.forEach(cb => {
                            if (cb !== this) {
                                cb.checked = this.checked;
                            }
                        });
                    } else {
                        // If any other operation is unchecked, uncheck the "*" operation
                        const allChecked = Array.from(document.querySelectorAll(
                                `.operation-checkbox[data-feature="${feature}"]`))
                            .filter(cb => cb.dataset.operation !== '1')
                            .every(cb => cb.checked);

                        const starCheckbox = document.querySelector(
                            `.operation-checkbox[data-feature="${feature}"][data-operation="1"]`
                        );
                        if (starCheckbox) {
                            starCheckbox.checked = allChecked;
                        }
                    }

                    // Update the "Select All" checkbox again after all operations
                    updateSelectAllCheckboxState();
                });
            });

            // Initialize the "Select All" checkbox state on page load
            updateSelectAllCheckboxState();
        });
    </script>
@endpush
