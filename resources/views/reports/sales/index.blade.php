@extends('layouts.app')

@if ($reportType == 'Product')
    @section('title', 'Product Sales Report')
@elseif ($reportType == 'Category')
    @section('title', 'Category Sales Report')
@elseif ($reportType == 'Department')
    @section('title', 'Department Sales Report')
@endif

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $reportType }} Sales Report</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">{{ $reportType }} Sales List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                <button type="button" class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="fas fa-file-export me-1"></i>
                                    Export {{ $reportType }} Sales Report
                                </button>

                                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="exportModalLabel">Export
                                                    {{ $reportType }}
                                                    Sales Report</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <form action="{{ $exportRoute }}" method="GET">
                                                    <div class="mb-3">
                                                        <label for="start_date" class="form-label fw-semibold">Start
                                                            Date</label>
                                                        <input type="date" class="form-control" id="start_date"
                                                            name="start_date" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="end_date" class="form-label fw-semibold">End
                                                            Date</label>
                                                        <input type="date" class="form-control" id="end_date"
                                                            name="end_date" required>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <button type="submit" class="btn btn-primary">Export</button>
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
                        <div>
                            @if ($reportType === 'Product')
                                @livewire('sales.product-sales-table', ['selectedOutletId' => $selectedOutletId])
                            @elseif ($reportType === 'Category')
                                @livewire('sales.category-sales-table', ['selectedOutletId' => $selectedOutletId])
                            @elseif ($reportType === 'Department')
                                @livewire('sales.department-sales-table', ['selectedOutletId' => $selectedOutletId])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
