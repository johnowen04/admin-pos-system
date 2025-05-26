<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Product')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Product</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Product List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                <button type="button" class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="fa fa-plus"></i>
                                    Import Product
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="exportModalLabel">Import Product From
                                                    Excel</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <!-- Import Form -->
                                                <form action="{{ route('import.products') }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="file" class="form-label fw-semibold">Select
                                                            File</label>
                                                        <input type="file" class="form-control" id="file"
                                                            name="file" required>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary">Import</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary btn-round ms-auto"
                                    onclick="window.location='{{ route('product.create') }}'">
                                    <i class="fa fa-plus"></i>
                                    Add Product
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($products->isEmpty())
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="fa fa-box-open fa-3x text-muted"></i>
                                </div>
                                <h4 class="mt-4">No Products Available</h4>
                                <p class="text-muted">
                                    There are no products in the system yet.
                                    <br>Add your first product or import products from Excel.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('product.create') }}" class="btn btn-primary me-2">
                                        <i class="fa fa-plus me-1"></i> Add Product
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#exportModal">
                                        <i class="fa fa-file-import me-1"></i> Import from Excel
                                    </button>
                                </div>
                            </div>
                        @else
                            <div id="product-table" wire:key="product-table">
                                @livewire('product-table')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

</html>
