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
                    <label for="departmentName">Department Name</label>
                    <input type="text" class="form-control" id="departmentName" name="name"
                        placeholder="Ex: Toko, Futsal, etc" value="{{ old('name', $department->name ?? '') }}"
                        placeholder="Enter department name" required />
                </div>

                <div class="form-group">
                    <label for="categoryList">Category List</label>
                    <div id="categoryList">
                        @livewire('category-search', ['departmentId' => $department->id ?? null])
                    </div>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
