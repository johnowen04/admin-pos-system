<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if (false)
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

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="featureSelect">Feature</label>
                        <select class="form-select form-control" id="featureSelect" name="feature_id">
                            <option value="" disabled selected>Select feature</option>
                            @foreach ($features as $feature)
                                <option value="{{ $feature->id }}"
                                    {{ old('feature_id', $permission->feature_id ?? '') == $feature->id ? 'selected' : '' }}>
                                    {{ $feature->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('feature_id'))
                            <div class="text-danger">
                                {{ $errors->first('feature_id') }}
                            </div>
                        @endif
                    </div>

                </div>

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="operationSelect">Operation</label>
                        <select class="form-select form-control" id="operationSelect" name="operation_id">
                            <option value="" disabled selected>Select operation</option>
                            @foreach ($operations as $operation)
                                <option value="{{ $operation->id }}"
                                    {{ old('operation_id', $permission->operation_id ?? '') == $operation->id ? 'selected' : '' }}>
                                    {{ $operation->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('operation_id'))
                            <div class="text-danger">
                                {{ $errors->first('operation_id') }}
                            </div>
                        @endif
                    </div>

                </div>

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="permissionSlug">Slug</label>

                        <div class="input-group">
                            <input type="text" class="form-control" id="permissionSlug" name="slug"
                                placeholder="Ex: permission.create" value="{{ old('slug', $permission->slug ?? '') }}"
                                required readonly />

                            <button class="btn btn-black btn-border" type="button" id="toggleSlugEdit">
                                Edit
                            </button>
                        </div>
                        @if ($errors->has('slug'))
                            <div class="text-danger">
                                {{ $errors->first('slug') }}
                            </div>
                        @endif
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
            const featureSelect = document.getElementById('featureSelect');
            const operationSelect = document.getElementById('operationSelect');
            const slugField = document.getElementById('permissionSlug');
            const toggleButton = document.getElementById('toggleSlugEdit');

            function updateSlugField() {
                let selectedFeature = featureSelect.value ? featureSelect.options[featureSelect.selectedIndex]
                    .text.toLowerCase() : null;
                const selectedOperation = operationSelect.value ? operationSelect.options[operationSelect
                    .selectedIndex].text.toLowerCase() : null;

                if (selectedFeature.includes(' ')) {
                    selectedFeature = selectedFeature
                        .split(' ')
                        .map(word => word?.[0])
                        .join('')
                        .toLowerCase();
                }

                if (selectedFeature && selectedOperation) {
                    slugField.value = `${selectedFeature}.${selectedOperation}`;
                } else {
                    slugField.value = '';
                }
            }

            featureSelect.addEventListener('change', updateSlugField);
            operationSelect.addEventListener('change', updateSlugField);


            toggleButton.addEventListener('click', function() {
                if (slugField.hasAttribute('readonly')) {
                    // Enable the password field and change button text to "Cancel"
                    slugField.removeAttribute('readonly');
                    toggleButton.textContent = 'Cancel';
                } else {
                    // Disable the password field and reset button text to "Edit"
                    slugField.setAttribute('readonly', true);
                    toggleButton.textContent = 'Edit';
                }
            });
        });
    </script>
@endpush
