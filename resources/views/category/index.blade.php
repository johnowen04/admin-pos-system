<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Category')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Category</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Category List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('category.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Category
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($categories->isEmpty())
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="fa fa-tags fa-3x text-muted"></i>
                                </div>
                                <h4 class="mt-4">No Categories Available</h4>
                                <p class="text-muted">
                                    There are no product categories in the system yet.
                                    <br>Click the button below to create your first category.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('category.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-1"></i> Add Category
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="category-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Shown in Menu</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->id }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td>{{ $category->department->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($category->is_shown)
                                                        <span class="badge bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="{{ route('category.edit', $category->id) }}"
                                                            class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('category.destroy', $category->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-danger"
                                                                data-toggle="tooltip" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this category?')">
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
            $('#category-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": 4
                    } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush

</html>
