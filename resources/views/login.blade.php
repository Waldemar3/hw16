@extends('layout')

@section('title')
    Login
@endsection

@section('content')
    авторизация – это установление авторства текста, основанное на количественных или качественно-количественных методах.
@endsection

@section('form')
    <div class="col-lg-7 col-md-12 col-pad-0 form-section">
        <div class="login-inner-form">
            <div class="details">
                <a href="#">
                    <img src="assets/img/logos/logo-2.png" alt="logo">
                </a>
                <h3>Sign into your account</h3>
                <form action="/index" method="GET">
                    <div class="form-group">
                        <input type="email" name="email" class="input-text" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <input type="password" name="Password" class="input-text" placeholder="Password">
                    </div>
                    <div class="checkbox clearfix">
                        <div class="form-check checkbox-theme">
                            <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        <a href="/forgot-password">Forgot Password</a>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-md btn-theme btn-block">Login</button>
                    </div>
                    <p class="none-2">Don't have an account?<a href="/register"> Register here</a></p>
                </form>
                <a href="{{ $url }}" class="btn btn-secondary btn-lg" role="button" aria-pressed="true">log in with discord</a>
            </div>
        </div>
    </div>
@endsection
