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
                        <label for="operationName">Operation Name</label>
                        <input type="text" class="form-control" id="operationName" name="name"
                            placeholder="Ex: View, Create, Edit, Delete etc"
                            value="{{ old('name', $operation->name ?? '') }}" required />
                    </div>
                </div>

                <div class="col-md-6 col-lg-6">

                    <div class="form-group">
                        <label for="opertaionSlug">Slug</label>

                        <div class="input-group">
                            <input type="text" class="form-control" id="operationSlug" name="slug"
                                placeholder="Ex: view, create, delete, edit, *, etc"
                                value="{{ old('slug', $operation->slug ?? '') }}" required readonly />

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
            const nameField = document.getElementById('operationName');
            const slugField = document.getElementById('operationSlug');
            const toggleButton = document.getElementById('toggleSlugEdit');

            function updateSlugField() {
                let operationName = nameField.value ? nameField.value : null;

                if (operationName.includes(' ')) {
                    operationName = operationName
                        .split(' ')
                        .map(word => word?.[0])
                        .join('')
                }

                if (operationName) {
                    slugField.value = `${operationName.toLowerCase()}`;
                } else {
                    slugField.value = '';
                }
            }

            nameField.addEventListener('change', updateSlugField);

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
