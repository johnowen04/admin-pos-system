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
            <h3 class="fw-bold mb-3">Add {{ $invoiceType }} Invoice</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('invoice.form', [
                    'action' => $action,
                    'method' => $method,
                    'invoiceType' => $invoiceType,
                    'invoice' => $invoice,
                    'outlets' => $outlets,
                    'products' => $products,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
