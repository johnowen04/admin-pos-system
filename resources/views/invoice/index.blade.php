<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@if ($invoiceType === 'Purchase')
    @section('title', 'Purchase Invoice')
@else
    @section('title', 'Sales Invoice')
@endif

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $invoiceType }}</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">{{ $invoiceType }} List</h4>
                            <button class="btn btn-primary btn-round ms-auto"
                                onclick="window.location=@if ($invoiceType === 'Purchase') '{{ route('purchase.create') }}' @else '{{ route('sales.create') }}' @endif">
                                <i class="fa fa-plus"></i>
                                Add {{ $invoiceType }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="invoice-table" wire:key="invoice-table">
                            @livewire('invoice-table', ['selectedOutletId' => $selectedOutletId, 'invoiceType' => $invoiceType])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

</html>
