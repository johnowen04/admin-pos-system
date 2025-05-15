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
            <div class="row-md-4">

                <div class="form-group">
                    <label for="employeename">Employee Name</label>
                    <input type="text" class="form-control" id="employeename" name="name"
                        placeholder="Ex: John Doe, Lorem Ipsum etc"
                        value="{{ old('name', $employee->name ?? '') }}" required />
                </div>

                <div class="form-group">
                    <label for="employeenip">Employee Identification Number</label>
                    <input type="text" class="form-control" id="employeenip" name="nip"
                        placeholder="Ex: CEO001, ADM002, MGR003 etc" value="{{ old('nip', $employee->nip ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Position</label>
                    <select class="form-select form-control" id="defaultSelect" name="roles_id">
                        <option value="" disabled selected>Select position</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('roles_id', $employee->roles_id ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="employeephone">Phone</label>
                    <input type="text" class="form-control" id="employeephone" name="phone"
                        placeholder="Ex: 08123456789, 0322123456" value="{{ old('phone', $employee->phone ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Set Outlet</label>
                    <select class="form-select form-control" id="defaultSelect" name="outlets[]" multiple>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ in_array($outlet->id, old('outlets', $selectedOutlets ?? [])) ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="employeeemail">Email</label>
                    <input type="email" class="form-control" id="employeeemail" name="email"
                        placeholder="Ex: employee@mail.com" value="{{ old('email', $employee->email ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="employeepassword">Password</label>

                    <div class="input-group">
                        <input type="password" class="form-control" id="employeepassword" name="password"
                            placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"
                            required @if ($method === 'PUT') readonly @endif />
                        @if ($method === 'PUT')
                            <button class="btn btn-black btn-border" type="button" id="togglePasswordEdit">
                                Edit
                            </button>
                        @endif
                    </div>
                    <small class="form-text text-muted">
                        @if ($method === 'PUT')
                            Leave this field empty if you don't want to change the password.
                        @else
                            Password is required for new employees.
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('employeepassword');
            const toggleButton = document.getElementById('togglePasswordEdit');

            toggleButton.addEventListener('click', function() {
                if (passwordField.readonly) {
                    // Enable the password field and change button text to "Cancel"
                    passwordField.readonly = false;
                    toggleButton.textContent = 'Cancel';
                } else {
                    // Disable the password field and reset button text to "Edit"
                    passwordField.readonly = true;
                    toggleButton.textContent = 'Edit';
                    passwordField.value = ''; // Clear the password field
                }
            });
        });
    </script>
@endpush
