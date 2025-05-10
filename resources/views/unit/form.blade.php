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

                <div class="col-md-2 col-lg-4">

                    <div class="form-group">
                        <label for="unitname">Unit Name</label>
                        <input type="text" class="form-control" id="unitname" name="name"
                            placeholder="Ex: Kilogram, Gram, Pieces etc" value="{{ old('name', $unit->name ?? '') }}"
                            required />
                    </div>

                </div>

                <div class="col-md-2 col-lg-4">

                    <div class="form-group">
                        <label for="unitconversion">Conversion Unit</label>
                        <input type="number" class="form-control" id="unitconversion" name="conversion_unit"
                            placeholder="Ex: 1, 12, 24, 1000"
                            value="{{ old('conversion_unit', $unit->conversion_unit ?? '') }}" required />
                    </div>

                </div>

                <div class="col-md-2 col-lg-4">

                    <div class="form-group">
                        <label for="defaultSelect">To Unit</label>
                        <select class="form-select form-control" id="defaultSelect" name="to_base_unit_id">
                            <option value="" disabled selected>Select conversion unit</option>
                            @foreach ($baseunits as $baseunit)
                                <option value="{{ $baseunit->id }}"
                                    {{ old('to_base_unit_id', $unit->to_base_unit_id ?? '') == $baseunit->id ? 'selected' : '' }}>
                                    {{ $baseunit->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('to_base_unit_id'))
                            <div class="text-danger">
                                {{ $errors->first('to_base_unit_id') }}
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
