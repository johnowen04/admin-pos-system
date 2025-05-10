<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Base Unit')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Base Unit</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('baseunit.form', [
                    'action' => $action,
                    'method' => $method,
                    'baseunit' => $baseunit,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
