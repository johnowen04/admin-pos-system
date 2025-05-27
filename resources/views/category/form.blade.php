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
                    <label for="outlet-select">Set Outlet</label>
                    <div id="outlet-select" wire:key="outlet-select">
                        @livewire('outlet-select', ['selectedOutletIds' => old('outlets', $selectedOutlets ?? [])])
                    </div>
                </div>

                <div class="form-group">
                    <label for="categoryName">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" name="name"
                        value="{{ old('name', $category->name ?? '') }}" placeholder="Enter category name" required />
                </div>

                <div class="form-group">
                    <label for="defaultSelect">Department</label>
                    <select class="form-select form-control" id="defaultSelect" name="department_id">
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
                    <label>Show in menu?</label>
                    <div class="d-flex align-items-center" x-data="{ isShown: {{ old('is_shown', $category->is_shown ?? 1) }} }">
                        <div class="form-check form-switch mb-1 d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" role="switch" id="showInMenuToggle"
                                name="is_shown" value="1" x-model="isShown"
                                {{ old('is_shown', $category->is_shown ?? 1) == 1 ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label m-0 d-flex align-items-center" for="showInMenuToggle">
                            <span x-text="isShown == 1 ? 'Yes' : 'No'">
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
