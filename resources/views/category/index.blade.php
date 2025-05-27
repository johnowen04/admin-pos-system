<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Category')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Category</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Category List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location='{{ route('category.create') }}'">
                                <i class="fa fa-plus"></i>
                                Add Category
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="category-table" wire:key="category-table">
                            @livewire('category-table', ['selectedOutletId' => $selectedOutletId])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

</html>
