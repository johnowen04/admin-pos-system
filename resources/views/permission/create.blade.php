<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Add Permission')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Add Permission</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('permission.form', [
                    'action' => $action,
                    'method' => $method,
                    'features' => $features,
                    'operations' => $operations,
                    'permission' => $permission,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
