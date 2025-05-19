<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Base Unit')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Base Unit</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('bu.form', [
                    'action' => $action,
                    'method' => $method,
                    'baseUnit' => $baseUnit,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
