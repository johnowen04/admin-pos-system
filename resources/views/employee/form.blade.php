<form action="{{ $action }}" method="POST" x-data="employeeForm()">
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
                        x-model="employeeName" required />
                </div>

                <div class="form-group">
                    <label for="employeeNIP">Employee Identification Number</label>
                    <input type="text" class="form-control" id="employeeNIP" name="nip"
                        placeholder="Ex: CEO001, ADM002, MGR003 etc" value="{{ old('nip', $employee->nip ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Position</label>
                    <select class="form-select form-control" id="defaultSelect" name="position_id"
                        x-model="selectedPosition" x-on:change="checkPosition()">
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
                    <label for="outlet-select">Set Outlet</label>

                    <div id="outlet-select" wire:key="outlet-select" x-show="isAdmin">
                        @livewire('outlet-select', ['selectedOutletIds' => old('outlets', $selectedOutlets ?? [])])
                    </div>

                    <div x-show="!isAdmin">
                        <select class="form-control" name="outlets[]" id="regular-outlet-select">
                            <option value="" disabled selected>Select outlet</option>
                            @foreach ($outlets ?? [] as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ in_array($outlet->id, old('outlets', $selectedOutlets ?? [])) ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Non-admin users can only be assigned to one outlet</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="employeePhone">Phone</label>
                    <input type="text" class="form-control" id="employeePhone" name="phone"
                        placeholder="Ex: 08123456789, 0322123456" value="{{ old('phone', $employee->phone ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="employeeEmail">Email</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="employeeEmail" name="email"
                            placeholder="Ex: employee@mail.com" value="{{ old('email', $employee->email ?? '') }}"
                            x-bind:readonly="!emailEditable" />
                        @if ($method === 'PUT')
                            <button class="btn btn-black btn-border" type="button" x-on:click="toggleEmailEdit"
                                x-text="emailButtonText">
                                Edit
                            </button>
                        @else
                            <button class="btn btn-black btn-border" type="button" x-on:click="generateEmail">
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
                                    name="createUserCheckbox" value="1" x-model="createUser"
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

                <div id="userCredentialsSection" x-show="showCredentials">
                    <div class="form-group mt-3">
                        <label for="employeeUsername">Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="employeeUsername" name="username"
                                placeholder="Ex: employee"
                                value="{{ old('username', $employee->user->username ?? '') }}"
                                x-bind:readonly="!usernameEditable" />
                            @if ($method === 'POST')
                                <button class="btn btn-black btn-border" type="button" x-on:click="generateUsername">
                                    Generate
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="employeePassword">Password</label>

                        <div class="input-group">
                            <input x-bind:type="passwordFieldType" class="form-control" id="employeePassword"
                                name="password"
                                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"
                                x-bind:readonly="!passwordEditable" />
                            @if ($method === 'PUT')
                                <button class="btn btn-black btn-border" type="button"
                                    x-on:click="togglePasswordEdit" x-text="passwordButtonText">
                                    Edit
                                </button>
                            @else
                                <button class="btn btn-black btn-border" type="button"
                                    x-on:click="generatePassword">
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
        function employeeForm() {
            return {
                employeeName: "{{ old('name', $employee->name ?? '') }}",
                emailEditable: false,
                passwordEditable: false,
                usernameEditable: {{ $method === 'POST' ? 'true' : 'false' }},
                passwordFieldType: 'password',
                createUser: {{ old('createUserCheckbox') ? 'true' : 'false' }},
                showCredentials: {{ ($method === 'PUT' && isset($employee->user)) || old('createUserCheckbox') ? 'true' : 'false' }},
                emailButtonText: 'Edit',
                passwordButtonText: 'Edit',
                selectedPosition: "{{ old('position_id', $employee->position_id ?? '') }}",
                isAdmin: {{ old('position_id', $employee->position_id ?? '') == config('constants.positions.admin', 2) ? 'true' : 'false' }},

                checkPosition() {
                    this.isAdmin = this.selectedPosition == {{ config('constants.positions.admin', 2) }};
                    console.log(this.isAdmin);
                },

                toggleEmailEdit() {
                    this.emailEditable = !this.emailEditable;
                    this.emailButtonText = this.emailEditable ? 'Cancel' : 'Edit';
                },

                togglePasswordEdit() {
                    this.passwordEditable = !this.passwordEditable;
                    this.passwordButtonText = this.passwordEditable ? 'Cancel' : 'Edit';

                    if (!this.passwordEditable) {
                        document.getElementById('employeePassword').value = '';
                    }
                },

                generateEmail() {
                    if (!this.employeeName) {
                        alert('Please enter employee name first');
                        return;
                    }

                    let emailName = this.employeeName.toLowerCase()
                        .replace(/\s+/g, '.')
                        .replace(/[^\w\.]/g, '')
                        .substring(0, 20);

                    const emailDomain = 'company.com';
                    document.getElementById('employeeEmail').value = `${emailName}@${emailDomain}`;
                },

                generateUsername() {
                    if (!this.employeeName) {
                        alert('Please enter employee name first');
                        return;
                    }

                    const username = this.employeeName.toLowerCase()
                        .replace(/\s+/g, '.')
                        .replace(/[^\w\.]/g, '')
                        .substring(0, 15);

                    document.getElementById('employeeUsername').value = username;
                },

                generatePassword() {
                    const length = 10;
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
                    let password = '';

                    for (let i = 0; i < length; i++) {
                        password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }

                    document.getElementById('employeePassword').value = password;
                    this.passwordFieldType = 'text';

                    setTimeout(() => {
                        this.passwordFieldType = 'password';
                    }, 3000);
                },

                init() {
                    this.checkPosition();
                    this.$watch('createUser', value => {
                        this.showCredentials = value;

                        if (!value) {
                            document.getElementById('employeeUsername').value = '';
                            document.getElementById('employeePassword').value = '';
                        }
                    });
                }
            }
        }
    </script>
@endpush
