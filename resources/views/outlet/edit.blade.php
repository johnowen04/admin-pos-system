<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Outlet')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Outlet</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('outlet.form', [
                    'action' => $action,
                    'method' => $method,
                    'outlet' => $outlet,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
