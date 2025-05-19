<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Unit')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Unit</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('unit.form', [
                    'action' => $action,
                    'method' => $method,
                    'unit' => $unit,
                    'baseUnits' => $baseUnits,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
