<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>
    <input type="text" class="form-control datepicker" id="{{ $id }}" name="{{ $name }}"
        placeholder="{{ $placeholder }}" value="{{ old($name, $value ?? '') }}" {{ $attributes }} />
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all datepickers
            flatpickr('.datepicker', {
                enableTime: true, // Enable time selection
                dateFormat: "Y-m-d H:i", // Format: YYYY-MM-DD HH:mm
            });
        });
    </script>
@endpush
