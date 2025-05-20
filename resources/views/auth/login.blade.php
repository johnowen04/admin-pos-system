@extends('layouts.app')

@section('title', 'Login')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Welcome to Admin POS System</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="/login" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <div class="row-md-4">

                                <div class="form-group">
                                    <label for="login">Email or Username</label>
                                    <input name="login" class="form-control" type="text" id="login"
                                        value="{{ old('login') }}" placeholder="Email" required autofocus>
                                    @error('login')
                                        <span>{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="loginPassword">Password</label>
                                    <input name="password" class="form-control" type="password" id="loginPassword"
                                        placeholder="Password" required>
                                    @error('password')
                                        <span>{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-action d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
