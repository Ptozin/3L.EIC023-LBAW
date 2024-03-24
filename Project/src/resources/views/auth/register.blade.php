@extends('layouts.app')

@section('content')

<section class="fakebodyREG">
    <div class="wrapper">
        <header>Register Form</header>
        
        <form form method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="field name">
            <div class="input-area">
            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Username" required >
            <i class="icon 	fa fa-user-alt"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('name'))
              <span class="error">
                  {{ $errors->first('name') }}
              </span>
            @endif
            </div>
            <div class="error error-txt">Name can't be blank</div>
        </div>

        <div class="field email">
            <div class="input-area">
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
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
      
        <div class="field conf password">
            <div class="input-area">
            <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <i class="icon fa fa-lock"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            </div>
            <div class="error error-txt">Password can't be blank</div>        
        </div>
    

        <div class="field address">
            <div class="input-area">
            <input id="address" type="text" name="address" value="{{ old('address') }}" placeholder="Address" required>
            <i class="icon fa fa-address-book"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('address'))
              <span class="error">
                  {{ $errors->first('address') }}
              </span>
            @endif
            </div>
            <div class="error error-txt">Address can't be blank</div>
        </div>

        <div class="field phone">
            <div class="input-area">
            <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" placeholder="Phone Number" required>
            <i class="icon fa fa-phone"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('phone_number'))
              <span class="error">
                  {{ $errors->first('phone_number') }}
              </span>
            @endif
            </div>
            <div class="error error-txt">Phone can't be blank</div>
        </div>

        <div class="field birth">
            <div class="input-area">
            <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}" placeholder="Birthdate" required>
            <i class="icon fa fa-birthday-cake"></i>
            <i class="error error-icon fa fa-exclamation-circle"></i>
            @if ($errors->has('birthdate'))
              <span class="error">
                  {{ $errors->first('birthdate') }}
              </span>
            @endif
            </div>
            <div class="error error-txt">Birthdate can't be blank</div>
        </div>

        <input type="submit" value="Register">

        </form>
        <div class="sign-txt">Already member? <a class="button button-outline" id="reglink" href="{{ route('login') }}">Login</a></div>
       
    </div>
</section>

@endsection
