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
                    <label for="baseUnitName">Base Unit Name</label>
                    <input type="text" class="form-control" id="baseUnitName" name="name"
                        placeholder="Ex: Kilogram, Gram, Pieces etc" value="{{ old('name', $baseUnit->name ?? '') }}"
                        required />
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
