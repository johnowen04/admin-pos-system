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
                                <button type="button" id="cancelEditBtn" class="btn btn-danger btn-round me-2"
                                    onclick="cancelEdit()" style="display: none;">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <button type="submit" id="saveChangesBtn" form="aclForm"
                                    class="btn btn-success btn-round me-2" style="display: none;">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <button id="toggleEditBtn" class="btn btn-primary btn-round" onclick="toggleEditMode()">
                                    <i class="fa fa-edit"></i> Edit ACL Matrix
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="aclForm" action="{{ $action }}" method="POST" onsubmit="prepareFormSubmission()">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            @if (empty($permissions))
                                <div class="empty-state text-center py-5">
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
                            @else
                                <div id="permissions-container">
                                    @foreach ($permissions as $feature => $operations)
                                        <div class="mb-4">
                                            <details class="border rounded shadow-sm p-3">
                                                <summary class="fw-bold">{{ ucfirst($feature) }}</summary>
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center">
                                                                    Operation
                                                                    <input type="checkbox"
                                                                        class="form-check-input ms-2 feature-all"
                                                                        data-feature="{{ $feature }}" disabled>
                                                                </th>
                                                                @foreach ($roles as $role)
                                                                    <th class="text-center">
                                                                        {{ $role }}
                                                                        <input type="checkbox"
                                                                            class="form-check-input ms-2 role-all"
                                                                            data-role="{{ $role }}" disabled>
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
                                                                            data-operation="{{ $operation }}" disabled>
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
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/app/acl-matrix.js') }}"></script>
@endpush