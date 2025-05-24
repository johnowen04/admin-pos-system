<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Outlet')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Outlet</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Outlet List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('outlet.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Outlet
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="outlet-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outlets as $outlet)
                                        <tr>
                                            <td>{{ $outlet->id }}</td>
                                            <td>{{ $outlet->name }}</td>
                                            <td>{{ $outlet->address }}</td>
                                            <td>
                                                @if ($outlet->type->value === 'pos')
                                                    <span class="badge bg-success text-white">Point of Sales</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Warehouse</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($outlet->status == 'open')
                                                    <span class="badge bg-success text-white">Open</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Closed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('outlet.edit', $outlet->id) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('outlet.destroy', $outlet->id) }}"
                                                        method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                            data-toggle="tooltip" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this outlet?')">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#outlet-table').DataTable({
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 5 } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush

</html>
