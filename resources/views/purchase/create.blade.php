<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Purchase Invoice')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Purchase Invoice</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('purchase.form', [
                    'action' => $action,
                    'method' => $method,
                    'purchase' => $purchase,
                    'outlets' => $outlets,
                    'products' => $products,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
