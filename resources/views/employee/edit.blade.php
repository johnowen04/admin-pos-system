<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Employee</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('employee.form', [
                    'action' => $action,
                    'method' => $method,
                    'employee' => $employee,
                    'outlets' => $outlets,
                    'selectedOutlets' => $selectedOutlets,
                    'positions' => $positions,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
