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
                        <label for="positionName">Position Name</label>
                        <input type="text" class="form-control" id="positionName" name="name"
                            placeholder="Ex: Administrator, Owner, Manager etc"
                            value="{{ old('name', $position->name ?? '') }}" required />
                    </div>
                </div>

                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="defaultSelect">Level</label>
                        <select class="form-select form-control" id="defaultSelect" name="level">
                            <option value="" disabled selected>Select level</option>
                            @foreach ($positionLevels as $level)
                                <option value="{{ $level->value }}"
                                    {{ old('level', $position->level->value ?? '') == $level->value ? 'selected' : '' }}>
                                    {{ $level->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('level'))
                            <div class="text-danger">
                                {{ $errors->first('level') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="row-md-4 mt-4 ms-2">
                <div id="position-permission-table" wire:key="position-permission-table">
                    @livewire('position-permission-table', ['position' => $position ?? null])
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
