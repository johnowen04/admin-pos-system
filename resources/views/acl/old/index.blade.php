<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'ACL Matrix')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">ACL Matrix</h4>
                            <div class="ms-auto">
                                <!-- Edit mode buttons (hidden by default) -->
                                <button type="button" id="cancelEditBtn" class="btn btn-danger btn-round me-2"
                                    onclick="cancelEdit()" style="display: none;">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <button type="submit" id="saveChangesBtn" form="aclForm"
                                    class="btn btn-success btn-round me-2" style="display: none;">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>

                                <!-- View mode button -->
                                <button id="toggleEditBtn" class="btn btn-primary btn-round" onclick="toggleEditMode()">
                                    <i class="fa fa-edit"></i> Edit ACL Matrix
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="aclForm" action="{{ $action }}" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')

                        @if (empty($permissions))
                            <div class="alert alert-info m-3">
                                <h5 class="alert-heading"><i class="fa fa-info-circle me-2"></i>No Permissions Data</h5>
                                <p class="mb-0">No permission data is available. Please create features, operations, and
                                    roles first.</p>
                            </div>
                        @else
                            <div class="row-md-4 mt-4 ms-2">
                                @foreach ($permissions as $feature => $operations)
                                    <div class="col-md-12 mb-4">
                                        <div class="card-body">
                                            <div class="row-md-4 mt-4 ms-2">
                                                @foreach ($permissions as $feature => $operations)
                                                    <div class="col-md-12 mb-4">
                                                        <details class="border rounded shadow-sm p-3" open>
                                                            <summary class="fw-bold">{{ ucfirst($feature) }}</summary>
                                                            <div class="table-responsive mt-3">
                                                                <table class="table table-bordered table-hover">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th class="text-center">
                                                                                Operation
                                                                                <input type="checkbox"
                                                                                    class="form-check-input ms-2 feature-all"
                                                                                    data-feature="{{ $feature }}"
                                                                                    disabled>
                                                                            </th>
                                                                            @foreach ($roles as $role)
                                                                                <th class="text-center">
                                                                                    {{ $role }}
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input ms-2 role-all"
                                                                                        data-role="{{ $role }}"
                                                                                        disabled>
                                                                                </th>
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($features[$feature] as $operation)
                                                                            <tr>
                                                                                <td class="text-center">
                                                                                    {{ ucfirst($operation) }}
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input ms-2 operation-all"
                                                                                        data-feature="{{ $feature }}"
                                                                                        data-operation="{{ $operation }}"
                                                                                        disabled>
                                                                                </td>
                                                                                @foreach ($roles as $role)
                                                                                    <td class="text-center">
                                                                                        <input type="checkbox"
                                                                                            class="form-check-input permission-checkbox"
                                                                                            data-feature="{{ $feature }}"
                                                                                            data-operation="{{ $operation }}"
                                                                                            data-role="{{ $role }}"
                                                                                            name="permissions[{{ $role }}][{{ $feature }}][{{ $operation }}]"
                                                                                            value="1" disabled
                                                                                            {{ isset($permissions[$feature][$operation][$role]) && $permissions[$feature][$operation][$role] == '1' ? 'checked' : '' }}>
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
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </form>

                    <!-- View-only version (shown by default) -->
                    <div id="viewOnlyContent" class="card-body">
                        @if (empty($permissions))
                            <div class="card-body text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fa fa-lock fa-3x text-muted"></i>
                                    </div>
                                    <h4 class="mt-4">No Permission Data Available</h4>
                                    <p class="text-muted">
                                        There are no permissions configured in the system.
                                        <br>Please create some features, operations, and roles first.
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{ route('role.index') }}" class="btn btn-primary me-2">
                                            <i class="fa fa-user-shield me-1"></i> Manage Roles
                                        </a>
                                        <a href="{{ route('feature.index') }}" class="btn btn-outline-primary">
                                            <i class="fa fa-tasks me-1"></i> Manage Features
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div id="viewOnlyContent" class="card-body">
                                <div class="row-md-4 mt-4 ms-2">
                                    @foreach ($permissions as $feature => $operations)
                                        <div class="col-md-12 mb-4">
                                            <details class="border rounded shadow-sm p-3" open>
                                                <summary class="fw-bold">{{ ucfirst($feature) }}</summary>
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center">Operation</th>
                                                                @foreach ($roles as $role)
                                                                    <th class="text-center">{{ $role }}
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($features[$feature] as $operation)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        {{ ucfirst($operation) }}</td>
                                                                    @foreach ($roles as $role)
                                                                        <td class="text-center">
                                                                            <input disabled type="checkbox"
                                                                                class="form-check-input"
                                                                                {{ isset($permissions[$feature][$operation][$role]) && $permissions[$feature][$operation][$role] == '1' ? 'checked' : '' }}>
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
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Store if we've already initialized event listeners
        let listenersInitialized = false;

        function toggleEditMode() {
            const viewOnlyContent = document.getElementById('viewOnlyContent');
            const aclForm = document.getElementById('aclForm');
            const toggleEditBtn = document.getElementById('toggleEditBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const saveChangesBtn = document.getElementById('saveChangesBtn');
            const checkboxes = document.querySelectorAll('#aclForm .permission-checkbox');
            const headerCheckboxes = document.querySelectorAll(
                '#aclForm .feature-all, #aclForm .role-all, #aclForm .operation-all');

            if (viewOnlyContent.style.display !== 'none') {
                // Switch to edit mode
                viewOnlyContent.style.display = 'none';
                aclForm.style.display = 'block';

                // Hide edit button, show cancel and save buttons
                toggleEditBtn.style.display = 'none';
                cancelEditBtn.style.display = 'inline-block';
                saveChangesBtn.style.display = 'inline-block';

                // Enable all checkboxes
                checkboxes.forEach(cb => cb.disabled = false);
                headerCheckboxes.forEach(cb => cb.disabled = false);

                // Initialize all header checkbox states and add event listeners (only once)
                if (!listenersInitialized) {
                    initializeCheckboxes();
                    listenersInitialized = true;
                }

                // Update states in case anything changed
                updateAllCheckboxStates();
            } else {
                cancelEdit();
            }
        }

        function cancelEdit() {
            const viewOnlyContent = document.getElementById('viewOnlyContent');
            const aclForm = document.getElementById('aclForm');
            const toggleEditBtn = document.getElementById('toggleEditBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const saveChangesBtn = document.getElementById('saveChangesBtn');

            // Switch to view mode
            viewOnlyContent.style.display = 'block';
            aclForm.style.display = 'none';

            // Show edit button, hide cancel and save buttons
            toggleEditBtn.style.display = 'inline-block';
            cancelEditBtn.style.display = 'none';
            saveChangesBtn.style.display = 'none';
        }

        function initializeCheckboxes() {
            // Your existing checkbox initialization code
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const featureAllCheckboxes = document.querySelectorAll('.feature-all');
            const roleAllCheckboxes = document.querySelectorAll('.role-all');
            const operationAllCheckboxes = document.querySelectorAll('.operation-all');

            // Add click handlers for all "select all" checkboxes
            featureAllCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const feature = this.dataset.feature;
                    const checkboxes = document.querySelectorAll(
                        `.permission-checkbox[data-feature="${feature}"]`);
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateAllCheckboxStates();
                });
            });

            roleAllCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const role = this.dataset.role;
                    const checkboxes = document.querySelectorAll(
                        `.permission-checkbox[data-role="${role}"]`);
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateAllCheckboxStates();
                });
            });

            operationAllCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const feature = this.dataset.feature;
                    const operation = this.dataset.operation;
                    const checkboxes = document.querySelectorAll(
                        `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]`
                    );
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateAllCheckboxStates();
                });
            });

            // Add change handlers for all permission checkboxes
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Special handling for "*" operation
                    const feature = this.dataset.feature;
                    const operation = this.dataset.operation;
                    const role = this.dataset.role;

                    // If "*" operation is checked, check all other operations for this feature and role
                    if (operation === '*' && this.checked) {
                        const featureRoleCheckboxes = document.querySelectorAll(
                            `.permission-checkbox[data-feature="${feature}"][data-role="${role}"]`
                        );
                        featureRoleCheckboxes.forEach(cb => cb.checked = true);
                    } else if (operation !== '*') {
                        // If any other operation is unchecked, uncheck the "*" operation
                        const starCheckbox = document.querySelector(
                            `.permission-checkbox[data-feature="${feature}"][data-role="${role}"][data-operation="*"]`
                        );
                        if (starCheckbox && !this.checked) {
                            starCheckbox.checked = false;
                        }

                        // If all specific operations are checked, check the "*" operation
                        const allActionsChecked = Array.from(document.querySelectorAll(
                            `.permission-checkbox[data-feature="${feature}"][data-role="${role}"]:not([data-operation="*"])`
                        )).every(cb => cb.checked);

                        if (starCheckbox && allActionsChecked) {
                            starCheckbox.checked = true;
                        }
                    }

                    // Update all header checkboxes
                    updateAllCheckboxStates();
                });
            });
        }

        function updateAllCheckboxStates() {
            // Your existing checkbox update code
            // Update feature checkboxes
            document.querySelectorAll('.feature-all:not([disabled])').forEach(checkbox => {
                const feature = checkbox.dataset.feature;
                const checkboxes = document.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"]:not([disabled])`);
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkbox.checked = allChecked && checkboxes.length > 0;
            });

            // Update role checkboxes
            document.querySelectorAll('.role-all:not([disabled])').forEach(checkbox => {
                const role = checkbox.dataset.role;
                const checkboxes = document.querySelectorAll(
                    `.permission-checkbox[data-role="${role}"]:not([disabled])`);
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkbox.checked = allChecked && checkboxes.length > 0;
            });

            // Update operation checkboxes
            document.querySelectorAll('.operation-all:not([disabled])').forEach(checkbox => {
                const feature = checkbox.dataset.feature;
                const operation = checkbox.dataset.operation;
                const checkboxes = document.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]:not([disabled])`
                );
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkbox.checked = allChecked && checkboxes.length > 0;
            });
        }
    </script>
@endpush

</html>
