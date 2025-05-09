<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Role</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('role.form', [
                    'action' => $action,
                    'method' => $method,
                    'role' => $role,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
