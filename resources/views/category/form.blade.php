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
                    <label for="categoryName">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" name="name"
                        value="{{ old('name', $category->name ?? '') }}" placeholder="Enter category name" required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Department</label>
                    <select class="form-select form-control" id="defaultSelect" name="departments_id">
                        <option value="" disabled selected>Select department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $category->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('departments_id'))
                        <div class="text-danger">
                            {{ $errors->first('departments_id') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>Show in menu?</label><br />
                    <div class="d-flex">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_shown" value="1"
                                id="flexRadioDefault1"
                                {{ old('is_shown', $category->is_shown ?? 1) == 1 ? 'checked' : '' }} />
                            <label class="form-check-label" for="flexRadioDefault1">
                                Show in menu
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_shown" value="0"
                                id="flexRadioDefault2"
                                {{ old('is_shown', $category->is_shown ?? 1) == 0 ? 'checked' : '' }} />
                            <label class="form-check-label" for="flexRadioDefault2">
                                Hide from menu
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
