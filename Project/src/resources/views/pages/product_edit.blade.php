@extends('layouts.app')

@section('title', 'SportsVerse - Product Editor')

@section('content')

<section class="nobackground">

    <a class="backlink" href="{!! route('product', $variation['id']) !!}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>
    </a>

    <div class="wrapper">
        <header>Product Editor</header>

        <form form method="POST" action="/api/product/{{ $variation['id'] }}">
            {{ csrf_field() }}

            <h4>Product Related Fields</h4>
            <br>

            <div class="field name">
                <p >Name:</p>
                <div class="input-area">
                <input id="name" type="text" name="name" value="{{$variation->product['name']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('name'))
                <span class="error">
                    {{ $errors->first('name') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field short_description">
                <p>Short Description:</p>
                <div class="input-area">
                <input id="short_description" type="text" name="short_description" value="{{$variation->product['short_description']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('short_description'))
                <span class="error">
                    {{ $errors->first('short_description') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field long_description">
                <p>Long Description:</p>
                <div class="input-area">
                <input id="long_description" type="text" name="long_description" value="{{$variation->product['long_description']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('long_description'))
                <span class="error">
                    {{ $errors->first('long_description') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field manufacturer">
                <p>Manufacturer:</p>
                <div class="input-area">
                <input id="manufacturer" type="text" name="manufacturer" value="{{$variation->product['manufacturer']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('manufacturer'))
                <span class="error">
                    {{ $errors->first('manufacturer') }}
                </span>
                @endif
                </div>
            </div>
            <br>

            <h4>Variation Related Fields</h4>
            <br>

            <div class="field stock">
                <p>Stock:</p>
                <div class="input-area">
                <input id="stock" type="numeric" min="0" name="stock" value="{{$variation['stock']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('stock'))
                <span class="error">
                    {{ $errors->first('stock') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field price">
                <p>Price:</p>
                <div class="input-area">
                <input id="price" type="numeric" step="0.01" min="0" name="price" value="{{$variation['price']}}" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('price'))
                <span class="error">
                    {{ $errors->first('price') }}
                </span>
                @endif
                </div>
            </div>

            <input id="submitbut" type="submit" value="Submit">

        </form>
       
    </div>
</section>


@endsection