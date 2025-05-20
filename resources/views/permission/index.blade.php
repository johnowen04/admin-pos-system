<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Permission')

@section('content')
    <div class="page-inner">
        @if (false)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="page-header">
            <h3 class="fw-bold mb-3">Permission</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Permission List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                <button class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                    data-bs-target="#addBatchPermissionModal">
                                    <i class="fa fa-plus"></i>
                                    Add Batch Permission
                                </button>
                                <button class="btn btn-primary btn-round "
                                    onclick="window.location='{{ route('permission.create') }}'">
                                    <i class="fa fa-plus"></i>
                                    Add Permission
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="permission-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Feature</th>
                                        <th>Operation</th>
                                        <th>SU Only?</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->id }}</td>
                                            <td>
                                                @if ($permission->feature)
                                                    {{ $permission->feature->name }}
                                                    @if ($permission->feature->trashed())
                                                        <span class="badge bg-warning text-dark" data-toggle="tooltip"
                                                            title="This feature has been deleted. Please update the feature or delete the permission.">
                                                            <i class="fa fa-exclamation-triangle"></i>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-danger">No feature</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($permission->operation)
                                                    {{ $permission->operation->name }}
                                                    @if ($permission->operation->trashed())
                                                        <span class="badge bg-warning text-dark" data-toggle="tooltip"
                                                            title="This operation has been deleted. Please update the operation or delete the permission.">
                                                            <i class="fa fa-exclamation-triangle"></i>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-danger">No operation</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label
                                                        class="form-check-label label-su-toggle @if ($permission->is_super_user_only) text-danger @else text-secondary @endif"
                                                        for="permissionSUToggle">{{ $permission->is_super_user_only ? 'Yes' : 'No' }}</label>
                                                    <input id="permissionSUToggle"
                                                        class="form-check-input permission-su-toggle" type="checkbox"
                                                        data-permission-id="{{ $permission->id }}"
                                                        {{ $permission->is_super_user_only ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('permission.edit', $permission->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('permission.destroy', $permission->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                            data-toggle="tooltip" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this permission?')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Batch Permission Modal -->
    <div class="modal fade" id="addBatchPermissionModal" tabindex="-1" aria-labelledby="addBatchPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBatchPermissionModalLabel">Add Batch Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('permission.batch') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="featureSelect">Feature</label>
                            <select class="form-select form-control" id="featureSelect" name="feature_id">
                                <option value="" disabled selected>Select Permission</option>
                                @foreach ($features as $feature)
                                    <option value="{{ $feature->id }}">
                                        {{ $feature->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('feature_id'))
                                <div class="text-danger">
                                    {{ $errors->first('feature_id') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="operations">Operations</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <input type="checkbox" id="toggleSelectOperations" class="form-check-input">
                                    <label for="toggleSelectOperations" class="form-check-label">Select All</label>
                                </div>
                            </div>
                            <select class="form-select form-control" id="operations" name="operations[]" multiple>
                                @foreach ($operations as $operation)
                                    <option value="{{ $operation->id }}" {{ $operation->id }}>
                                        {{ $operation->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple
                                operations.</small>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            // Initialize DataTable
            const permissionTable = $('#permission-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }]
            });

            const toggleOperation = document.getElementById('toggleSelectOperations');
            const operationsSelect = document.getElementById('operations');

            toggleOperation.addEventListener('change', function() {
                const isChecked = this.checked;
                const options = operationsSelect.options;

                for (let i = 0; i < options.length; i++) {
                    options[i].selected = isChecked;
                }

                operationsSelect.dispatchEvent(new Event('change'));
            });

            // Use event delegation for the toggle switches
            // This attaches the handler to the table body which is always present
            $('#permission-table tbody').on('change', '.permission-su-toggle', function() {
                const permissionId = this.dataset.permissionId;
                const isSuperUserOnly = this.checked ? 1 : 0;
                const toggleElement = this;
                const row = this.closest('tr');

                // Display permission details for better alerts
                const featureCell = row.querySelector('td:nth-child(2)');
                const operationCell = row.querySelector('td:nth-child(3)');
                const permissionName =
                    `${featureCell.textContent.trim()}.${operationCell.textContent.trim()}`;

                // Show loading state
                toggleElement.disabled = true;
                row.classList.add('table-warning');

                // Create form data
                const formData = new FormData();
                formData.append('permission_id', permissionId);
                formData.append('is_super_user_only', isSuperUserOnly);
                formData.append('_token', '{{ csrf_token() }}');

                // Fetch API
                fetch('{{ route('permission.toggle-superuser') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Server responded with an error');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Success handling
                        alert(data.message || 'Permission updated successfully');
                        row.classList.remove('table-warning');
                        row.classList.add('table-success');
                        setTimeout(() => row.classList.remove('table-success'), 2000);
                    })
                    .catch(error => {
                        // Error handling
                        let errorMsg = 'Failed to update permission';
                        console.error('Error:', error);

                        // Revert toggle state
                        toggleElement.checked = !isSuperUserOnly;
                        alert(errorMsg);

                        // Visual feedback for error
                        row.classList.remove('table-warning');
                        row.classList.add('table-danger');
                        setTimeout(() => row.classList.remove('table-danger'), 2000);
                    })
                    .finally(() => {
                        // Always run this
                        toggleElement.disabled = false;
                        setTimeout(() => {
                            row.classList.remove('table-warning');
                            // Update the label text if needed
                            const label = row.querySelector('.label-su-toggle');
                            if (label) {
                                label.textContent = toggleElement.checked ? 'Yes' : 'No';
                                label.classList.toggle('text-danger', toggleElement.checked);
                                label.classList.toggle('text-secondary', !toggleElement
                                .checked);
                            }
                        }, 500);
                    });
            });
        });
    </script>
@endpush

</html>
