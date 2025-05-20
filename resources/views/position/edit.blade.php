<!DOCTYPE html>
<html lang="en">

@extends('layouts.app')

@section('title', 'Edit Position')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Position</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('position.form', [
                    'action' => $action,
                    'method' => $method,
                    'position' => $position,
                    'cancelRoute' => $cancelRoute,
                ])
            </div>
        </div>
    </div>
@endsection

</html>
