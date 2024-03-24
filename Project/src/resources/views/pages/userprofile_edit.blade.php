@extends('layouts.app')

@section('title', 'SportsVerse - User Profile Editor')

@section('content')

<section class="nobackground">

    <a class="backlink" href="{!! route('profile', $user['id']) !!}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>
    </a>

    <div class="wrapper">
        <header>Profile Editor</header>

        <form form method="POST" action="/api/user/{{ $user['id'] }}">
            {{ csrf_field() }}

            <div class="field email">
                <div class="input-area">
                <input id="email" type="email" name="email" value="{{$user['email']}}" disabled>
                <i class="icon fa fa-envelope"></i>
                @if ($errors->has('email'))
                <span class="error">
                    {{ $errors->first('email') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field name">
                <div class="input-area">
                <input id="name" type="text" name="name" value="{{$user['name']}}" required autofocus>
                <i class="icon 	fa fa-user-alt"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('name'))
                <span class="error">
                    {{ $errors->first('name') }}
                </span>
                @endif
                </div>
            </div>

            @if (!Auth::user()->is_admin)
            <div class="field password">
                <div class="input-area">
                <input id="password" type="password" name="password" minlength="6" placeholder="Password" required>
                <i class="icon fa fa-lock"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('password'))
                    <span class="error">
                        {{ $errors->first('password') }}
                    </span>
                @endif
                </div>
            
            </div>

        
            <div class="field conf password">
                <div class="input-area">
                <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                <i class="icon fa fa-lock"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                </div>     
            </div>
            @endif
        

            <div class="field address">
                <div class="input-area">
                <input id="address" type="text" name="address" value="{{$user['address']}}" placeholder="Address" required>
                <i class="icon fa fa-address-book"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('address'))
                <span class="error">
                    {{ $errors->first('address') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field phone">
                <div class="input-area">
                <input id="phone_number" type="tel" name="phone_number" value="{{$user['phone_number']}}" placeholder="Phone Number" required>
                <i class="icon fa fa-phone"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('phone_number'))
                <span class="error">
                    {{ $errors->first('phone_number') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field birth">
                <div class="input-area">
                <input id="birthdate" type="date" name="birthdate" value="{{$user['birthdate']}}" placeholder="Birthdate" required>
                <i class="icon fa fa-birthday-cake"></i>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('birthdate'))
                <span class="error">
                    {{ $errors->first('birthdate') }}
                </span>
                @endif
                </div>
            </div>

            <br>

            <input id="submitbut" type="submit" value="Submit">
        </form>

        <div id="butflex">
            <form method="POST" action="{{route('deleteuser', $user->id)}}">
                {{ csrf_field() }}

                @include('includes.validation')

                @if(session()->has('deleteconfirmation'))
                    <div><strong>{{session()->get('deleteconfirmation')}}</strong></div>
                    <div id="smallerPlease">Once you delete this account, all your reviews will remain visible on the site but without your name attatched.</div>
                    <input id="limitHeight" type="password" name="userpassword" required placeholder="Enter your password to delete your account">
                    <input class="deletebut" name="submit" type="submit" value="Delete">
                @else 
                    <input class="deletebut" name="submit" type="submit" value="Delete Account">
                @endif

            </form>
            @php $admin = Auth::user(); @endphp
            @if($admin->is_admin)
            <form id="blockform" method="POST" action="{{route('blockuser', $user->id)}}">
                {{ csrf_field() }}
                @if($user->blocked)
                    <input style="background-color:rgb(33, 151, 49);" class="blockbut" name="submit" type="submit" value="Unblock">
                @else
                    <input class="blockbut" name="submit" type="submit" value="Block">
                @endif
            </form>
            @endif
        </div>
       
    </div>
</section>


@endsection