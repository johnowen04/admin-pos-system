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
            $('#permission-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // Disable sorting on the Action column
                ]
            });

            const toggleOperation = document.getElementById('toggleSelectOperations');
            const operationsSelect = document.getElementById('operations');


            toggleOperation.addEventListener('change', function() {
                const isChecked = this.checked; // Check if the toggle is checked
                const options = operationsSelect.options;

                // Select/Deselect all options
                for (let i = 0; i < options.length; i++) {
                    options[i].selected = isChecked;
                }

                // Trigger change event to update UI (if needed)
                operationsSelect.dispatchEvent(new Event('change'));
            });
        });
    </script>
@endpush

</html>
