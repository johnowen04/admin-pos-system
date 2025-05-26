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
                            <div class="d-flex flex-wrap gap-2 ms-auto justify-content-end">
                                <button type="button"
                                    class="btn btn-secondary btn-round text-sm-start fs-6 fs-md-5 fs-lg-4 col-sm-auto"
                                    data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fa fa-plus"></i>
                                    Import Product
                                </button>

                                <button class="btn btn-primary btn-round text-sm-start fs-6 fs-md-5 fs-lg-4 col-sm-auto"
                                    onclick="window.location='{{ route('product.create') }}'">
                                    <i class="fa fa-plus"></i>
                                    Add Product
                                </button>

                                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="exportModalLabel">Import Product From
                                                    Excel</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
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
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="product-table" wire:key="product-table">
                            @livewire('product-table', ['selectedOutletId' => $selectedOutletId])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

</html>
