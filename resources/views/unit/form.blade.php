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
                    <label for="unitname">Unit Name</label>
                    <input type="text" class="form-control" id="unitname" name="name"
                        placeholder="Ex: Kilogram, Gram, Pieces etc" value="{{ old('name', $unit->name ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="unitconversion">Unit Name</label>
                    <input type="number" class="form-control" id="unitconversion" name="conversion_unit"
                        placeholder="Ex: 1, 12, 24, 1000" value="{{ old('conversion_unit', $unit->conversion_unit ?? '') }}"
                        required />
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
