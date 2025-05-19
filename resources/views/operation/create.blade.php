<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Operation')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Operation</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('operation.form', [
                    'action' => $action,
                    'method' => $method,
                    'operation' => $operation,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
