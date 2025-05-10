@extends('layouts.app')

@section('title', 'Register')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Welcome to Admin POS System</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('register') }}" method="POST">
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
                                    <label for="registerName">Name</label>
                                    <input name="name" class="form-control" type="text" id="registerName"
                                        placeholder="Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="registerEmail">Email</label>
                                    <input name="email" class="form-control" type="email" id="registerEmail"
                                        placeholder="Email" required>
                                </div>

                                <div class="form-group">
                                    <label for="registerPassword">Password</label>
                                    <input name="password" class="form-control" type="password" id="registerPassword"
                                        placeholder="Password" required>
                                </div>

                                <div class="form-group">
                                    <label for="registerPasswordConfirm">Password</label>
                                    <input name="password_confirmation" class="form-control" type="password" id="registerPasswordConfirm"
                                        placeholder="Password Confirmation" required>
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
