@extends('layouts.app')

@section('content')

<section class="fakebody">
    <div class="wrapper">
        <header>Login Form</header>
        
        <form form method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="field email">
            <div class="input-area">
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
            <i class="icon fa fa-envelope"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('email'))
                <span class="error">
                {{ $errors->first('email') }}
                </span>
            @endif
            </div>
            <div class="error error-txt">Email can't be blank</div>
        </div>

        <div class="field password">
            <div class="input-area">
            <input id="password" type="password" name="password" placeholder="Password" required>
            <i class="icon fa fa-lock"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('password'))
                <span class="error">
                    {{ $errors->first('password') }}
                </span>
            @endif
            </div>
            <div class="error error-txt">Password can't be blank</div>
        </div>

        <input type="submit" value="Login">

        </form>
        <div class="sign-txt">Not yet member? <a class="button button-outline" id="reglink" href="{{ route('register') }}">Register</a></div>
    </div>
</section>

@endsection
