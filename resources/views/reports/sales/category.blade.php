<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Category Sales Report')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Category Sales Report</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Category Sales List</h4>
                            <div class="d-flex gap-1 ms-auto">
                                <!-- Button to trigger modal -->
                                <button type="button" class="btn btn-secondary btn-round" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="fas fa-file-export me-1"></i>
                                    Export Category Sales Report
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="exportModalLabel">Export Category
                                                    Sales Report</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <!-- Export Form -->
                                                <form action="{{ route('reports.sales.category.export') }}" method="GET">
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
                        @if ($report->rows()->isEmpty())
                            <div class="empty-state text-center py-5">
                                <div class="empty-state-icon">
                                    <i class="fa fa-chart-bar fa-3x text-muted"></i>
                                </div>
                                <h4 class="mt-4">No Sales Data Available</h4>
                                <p class="text-muted">
                                    There are no product sales records to display in the selected period.
                                    <br>Try selecting a different date range or create some sales first.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('pos.index') }}" class="btn btn-primary me-2">
                                        <i class="fa fa-cash-register me-1"></i> Create Sales
                                    </a>
                                    <button type="button" class="btn btn-secondary"
                                        onclick="document.getElementById('salesFilterForm').reset();">
                                        <i class="fa fa-filter me-1"></i> Reset Filters
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="category-sales-table" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Quantity Sold</th>
                                            <th>Total Sold (Rp)</th>
                                            <th>Total Qty (%)</th>
                                            <th>Total Sold (%)</th>
                                            <th>COGS (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report->rows() as $category)
                                            <tr>
                                                <td>{{ $category['name'] }}</td>
                                                <td>{{ $category['sold_quantity'] }}</td>
                                                <td>{{ $category['percentage_qty'] }}%</td>
                                                <td>Rp{{ $category['total_sold'] }}</td>
                                                <td>{{ $category['percentage_revenue'] }}%</td>
                                                <td>Rp{{ $category['total_sold_cogs'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#category-sales-table').DataTable({
                "pageLength": 10,
                "order": [
                    [0, "asc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": -1
                    } // Disable sorting on the Action column
                ]
            });
        });
    </script>
@endpush

</html>
