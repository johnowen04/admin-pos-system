<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Department</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('department.form', [
                    'action' => $action,
                    'method' => $method,
                    'department' => $department,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
