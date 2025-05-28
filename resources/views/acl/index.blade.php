@extends('layouts.app')

@section('title', 'ACL Matrix')

@section('content')
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div id="acl-matrix" wire:key="acl-matrix">
                    <form action="{{ $action }}" method="POST">
                        @csrf
                        @method('PUT')
                        @livewire('acl-matrix')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
