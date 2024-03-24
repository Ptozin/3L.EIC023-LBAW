@extends('layouts.app')

@section('title', 'SportsVerse - Add Product')

@section('content')

<section class="nobackground">

    <a class="backlink" href="{!! route('product', $variation['id']) !!}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>
    </a>

    <div class="wrapper">
        <header>Add Product</header>

        <form form method="POST" action="/api/productvariation/{{ $variation->product['id'] }}">
            {{ csrf_field() }}

            <br>

            <div class="field stock">
                <p>Stock:</p>
                <div class="input-area">
                <input id="stock" type="numeric" min="0" name="stock" required autofocus>
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
                <input id="price" type="numeric" step="0.01" min="0" name="price" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('price'))
                <span class="error">
                    {{ $errors->first('price') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field id_color">
                <p>Color ID:</p>
                <div class="input-area">
                <input id="id_color" type="numeric" min="0" name="id_color" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('id_color'))
                <span class="error">
                    {{ $errors->first('id_color') }}
                </span>
                @endif
                </div>
            </div>

            <div class="field id_size">
                <p>Size ID:</p>
                <div class="input-area">
                <input id="id_size" type="numeric" min="0" name="id_size" required autofocus>
                <i class="error error-icon fa fa-exclamation-circle"></i>
                @if ($errors->has('id_size'))
                <span class="error">
                    {{ $errors->first('id_size') }}
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