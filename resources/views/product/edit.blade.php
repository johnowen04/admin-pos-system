<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Product</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('product.form', [
                    'action' => $action,
                    'method' => $method,
                    'product' => $product,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
