@extends('layouts.app')

@section('title', 'SportsVerse - Add Category')

@section('content')

<section class="nobackground">

    <a class="backlink" href="{!! route('profile') !!}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>
    </a>

    <div class="wrapper">
        <header>Add Category</header>

        <form form method="POST" action="/api/category">
            {{ csrf_field() }}

            <div class="field name">
                <p >Name:</p>
                <div class="input-area">
                <input id="name" type="text" name="name" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('name'))
                <span class="error">
                    {{ $errors->first('name') }}
                </span>
                @endif
                </div>
            </div>
            <br>

            <input id="submitbut" type="submit" value="Submit">

        </form>
       
    </div>
</section>


@endsection