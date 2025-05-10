<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Category</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('category.form', [
                    'action' => $action,
                    'method' => $method,
                    'category' => $category,
                    'outlets' => $outlets,
                    'selectedOutlets' => $selectedOutlets,
                    'departments' => $departments,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSelectedOutlets() {
            const select = document.getElementById('defaultSelect');
            const selectedOptions = Array.from(select.selectedOptions).map(option => option.text);
            const selectedOutletsList = document.getElementById('selectedOutlets');

            // Clear the current list
            selectedOutletsList.innerHTML = '';

            // Add selected options to the list
            if (selectedOptions.length > 0) {
                selectedOptions.forEach(option => {
                    const li = document.createElement('li');
                    li.textContent = option;
                    selectedOutletsList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'None';
                selectedOutletsList.appendChild(li);
            }
        }
    </script>
@endpush

</html>
