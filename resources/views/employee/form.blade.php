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
                    <label for="employeeName">Employee Name</label>
                    <input type="text" class="form-control" id="employeeName" name="name"
                        placeholder="Ex: John Doe, Lorem Ipsum etc" value="{{ old('name', $employee->name ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="employeeNIP">Employee Identification Number</label>
                    <input type="text" class="form-control" id="employeeNIP" name="nip"
                        placeholder="Ex: CEO001, ADM002, MGR003 etc" value="{{ old('nip', $employee->nip ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Position</label>
                    <select class="form-select form-control" id="defaultSelect" name="position_id">
                        <option value="" disabled selected>Select position</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}"
                                {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="employeePhone">Phone</label>
                    <input type="text" class="form-control" id="employeePhone" name="phone"
                        placeholder="Ex: 08123456789, 0322123456" value="{{ old('phone', $employee->phone ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="outletSelect">Set Outlet</label>
                    <select class="form-select form-control" id="outletSelect" name="outlets[]" multiple>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}"
                                {{ in_array($outlet->id, old('outlets', $selectedOutlets ?? [])) ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="employeeEmail">Email</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="employeeEmail" name="email"
                            placeholder="Ex: employee@mail.com" value="{{ old('email', $employee->email ?? '') }}"
                            @if ($method === 'PUT') readonly @endif />
                        @if ($method === 'PUT')
                            <button class="btn btn-black btn-border" type="button" id="toggleEmailEdit">
                                Edit
                            </button>
                        @else
                            <button class="btn btn-black btn-border" type="button" id="generateEmail">
                                Generate
                            </button>
                        @endif
                    </div>
                    @if ($method === 'POST')
                        <small class="form-text text-muted">
                            Click generate to create an email based on employee name.
                        </small>
                    @endif
                </div>

                @if ($method !== 'PUT')
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="createUser"
                                    name="createUserCheckbox" value="1"
                                    {{ old('createUserCheckbox') ? 'checked' : '' }}>
                                <label class="form-check-label" for="createUser">
                                    Create user login?
                                </label>
                                <small class="form-text text-muted d-block">
                                    If checked, system will create the user login for this employee.
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <div id="userCredentialsSection"
                    style="display: {{ ($method === 'PUT' && isset($employee->user)) || old('createUserCheckbox') ? 'block' : 'none' }};">
                    <div class="form-group mt-3">
                        <label for="employeeUsername">Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="employeeUsername" name="username"
                                placeholder="Ex: employee"
                                value="{{ old('username', $employee->user->username ?? '') }}"
                                @if ($method === 'PUT') readonly @endif />
                            @if ($method === 'POST')
                                <button class="btn btn-black btn-border" type="button" id="generateUsername">
                                    Generate
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="employeePassword">Password</label>

                        <div class="input-group">
                            <input type="password" class="form-control" id="employeePassword" name="password"
                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"
                                @if ($method === 'PUT') readonly @endif />
                            @if ($method === 'PUT')
                                <button class="btn btn-black btn-border" type="button" id="togglePasswordEdit">
                                    Edit
                                </button>
                            @else
                                <button class="btn btn-black btn-border" type="button" id="generatePassword">
                                    Generate
                                </button>
                            @endif
                        </div>
                        <small class="form-text text-muted">
                            @if ($method === 'PUT')
                                Leave this field empty if you don't want to change the password.
                            @else
                                Password is required for new employees with user accounts.
                            @endif
                        </small>
                    </div>
                </div>

            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all form elements
            const createUserCheckbox = document.getElementById('createUser');
            const userCredentialsSection = document.getElementById('userCredentialsSection');
            const usernameField = document.getElementById('employeeUsername');
            const passwordField = document.getElementById('employeePassword');
            const emailField = document.getElementById('employeeEmail');

            // Edit buttons
            const toggleEmailButton = document.getElementById('toggleEmailEdit');
            const togglePasswordButton = document.getElementById('togglePasswordEdit');

            // Generate buttons
            const generateEmailBtn = document.getElementById('generateEmail');
            const generateUsernameBtn = document.getElementById('generateUsername');
            const generatePasswordBtn = document.getElementById('generatePassword');

            // Handle toggle password edit button if it exists
            if (togglePasswordButton) {
                togglePasswordButton.addEventListener('click', function() {
                    if (passwordField.hasAttribute('readonly')) {
                        // Enable the password field and change button text to "Cancel"
                        passwordField.removeAttribute('readonly');
                        togglePasswordButton.textContent = 'Cancel';
                    } else {
                        // Disable the password field and reset button text to "Edit"
                        passwordField.setAttribute('readonly', true);
                        togglePasswordButton.textContent = 'Edit';
                        passwordField.value = ''; // Clear the password field
                    }
                });
            }

            // Handle toggle email edit button if it exists
            if (toggleEmailButton) {
                toggleEmailButton.addEventListener('click', function() {
                    if (emailField.hasAttribute('readonly')) {
                        // Enable the email field and change button text to "Cancel"
                        emailField.removeAttribute('readonly');
                        toggleEmailButton.textContent = 'Cancel';
                    } else {
                        // Disable the email field and reset button text to "Edit"
                        emailField.setAttribute('readonly', true);
                        toggleEmailButton.textContent = 'Edit';
                    }
                });
            }

            // Handle create user checkbox functionality if it exists
            if (createUserCheckbox) {
                // Set initial state
                function updateFieldsState() {
                    if (createUserCheckbox.checked) {
                        userCredentialsSection.style.display = 'block';
                    } else {
                        userCredentialsSection.style.display = 'none';
                        // Clear fields when hiding
                        usernameField.value = '';
                        passwordField.value = '';
                    }
                }

                if (generateEmailBtn) {
                    generateEmailBtn.addEventListener('click', function() {
                        const employeeName = document.getElementById('employeeName').value.trim();
                        if (employeeName) {
                            // Convert name to lowercase, replace spaces with dots, remove special chars
                            let emailName = employeeName.toLowerCase()
                                .replace(/\s+/g, '.')
                                .replace(/[^\w\.]/g, '')
                                .substring(0, 20); // Limit to 20 chars

                            // Add domain - you can change this to your company domain
                            const emailDomain = 'company.com';
                            const email = `${emailName}@${emailDomain}`;

                            // If in edit mode and field is readonly, first enable it
                            if (emailField && emailField.hasAttribute('readonly')) {
                                emailField.removeAttribute('readonly');
                                if (toggleEmailButton) {
                                    toggleEmailButton.textContent = 'Cancel';
                                }
                            }

                            // Set the email value
                            if (emailField) {
                                emailField.value = email;
                            }
                        } else {
                            alert('Please enter employee name first');
                        }
                    });
                }

                // Toggle visibility of credentials section based on checkbox
                createUserCheckbox.addEventListener('change', function() {
                    updateFieldsState();
                });

                // Username generation functionality
                if (generateUsernameBtn) {
                    generateUsernameBtn.addEventListener('click', function() {
                        const employeeName = document.getElementById('employeeName').value.trim();
                        if (employeeName) {
                            // Convert name to lowercase, replace spaces with dots, remove special chars
                            let username = employeeName.toLowerCase()
                                .replace(/\s+/g, '.')
                                .replace(/[^\w\.]/g, '')
                                .substring(0, 15); // Limit to 15 chars

                            usernameField.value = username;
                        } else {
                            alert('Please enter employee name first');
                        }
                    });
                }

                // Password generation functionality
                if (generatePasswordBtn) {
                    generatePasswordBtn.addEventListener('click', function() {
                        const length = 10;
                        const chars =
                            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
                        let password = '';

                        for (let i = 0; i < length; i++) {
                            password += chars.charAt(Math.floor(Math.random() * chars.length));
                        }

                        passwordField.value = password;
                        // Show the generated password briefly
                        passwordField.type = 'text';
                        setTimeout(() => {
                            passwordField.type = 'password';
                        }, 3000);
                    });
                }

                // Initialize state on page load
                updateFieldsState();
            }
        });
    </script>
@endpush
