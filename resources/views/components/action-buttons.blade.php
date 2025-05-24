<div class="card-action d-flex justify-content-end gap-2">
    @if (str_contains($submitRoute, 'void'))
        <a href="{{ $cancelRoute }}" class="btn btn-primary">Cancel</a>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
            data-bs-target="#voidConfirmModal">Void</button>
    @else
        <a href="{{ $cancelRoute }}" class="btn btn-danger">Cancel</a>
        <button type="submit" formaction="{{ $submitRoute }}" class="btn btn-success">Submit</button>
    @endif
</div>
