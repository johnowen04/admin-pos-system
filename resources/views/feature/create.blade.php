<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Feature')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Feature</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('feature.form', [
                    'action' => $action,
                    'method' => $method,
                    'feature' => $feature,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
