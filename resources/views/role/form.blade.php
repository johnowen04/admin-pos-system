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
            <div class="row">

                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="rolename">Role Name</label>
                        <input type="text" class="form-control" id="rolename" name="name"
                            placeholder="Ex: Super User, Employee, etc"
                            value="{{ old('name', $role->name ?? '') }}" required />
                    </div>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>