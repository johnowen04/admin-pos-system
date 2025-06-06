@extends('layouts.app')

@section('title', 'Department')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Department</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Department List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('department.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Department
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($departments->isEmpty())
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="fa fa-sitemap fa-3x text-muted"></i>
                                </div>
                                <h4 class="mt-4">No Departments Available</h4>
                                <p class="text-muted">
                                    There are no departments in the system yet.
                                    <br>Click the button below to create your first department.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('department.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-1"></i> Add Department
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="department-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departments as $department)
                                            <tr>
                                                <td>{{ $department->id }}</td>
                                                <td>{{ $department->name }}</td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="{{ route('department.edit', $department->id) }}"
                                                            class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('department.destroy', $department->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-danger"
                                                                data-toggle="tooltip" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this department?')">
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#department-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": 2
                    } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush
