<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit {{ $invoiceType }} Invoice')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit {{ $invoiceType }} Invoice</h3>
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
