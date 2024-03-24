@extends('layouts.app')

@section('title', 'Wishlist')

@section('content')

<script src={{ asset('js/products.js') }} defer></script>
<script src={{ asset('js/wishlist.js') }} defer></script>

<a class="backlink" href="{{ url('/homepage') }}">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<div class="bg-background-off p-6 my-6 rounded">
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Your Wishlist</h1>
                <p class="lead fw-normal text-white-50 mb-0">Add more products to keep track</p>
            </div>
        </div>
    </header>

    <div class="container px-4 px-lg-5 mt-5">
        <div class="d-flex">
            <h1>My Favourite Products</h1>
        </div><br>
        @foreach(Auth::user()->notifications()->get() as $notification)
            @if($notification->notification_type == 'Product available')    
                @php $notify_stock = 1; 
                $not_stock_type = $notification->notification_type;  
                @endphp
            @endif
        @endforeach
        @if(isset($notify_stock))
            <div class="notifications">
                @if(isset($notify_stock))
                    <form method="POST" action="{{ route('notification',  $not_stock_type) }}">
                    @csrf {{ csrf_field() }} 
                    <h3>
                    One of the products in your wishlist is now in stock! 
                    </h3>
                    <input id="checkoutbtn" class="btn btn-dark btn-primary btn-lg btn-block" value="Confirm" type="submit">
                    </form>                
                @endif
            </div>
        @endif
        <div class="alert alert-success" id="cart-success" style="display:none"></div>
        <div class="alert alert-danger" id="cart-error" style="display:none"></div> 
        <div class="section-container">
            @foreach($variations as $variation)
                <div class="cart-item row">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm col-xs">
                        <div class="d-flex flex-column align-items-center cart_item_img">
                        <a href="{{ route('product', [$variation, $variation->id]) }}">
                            @if(count($variation->product_images) > 0)
                                @php $url = $variation->product_images->first()->url; @endphp
                                <img id="imgdetails" src="{{ asset("images/$url") }}" alt="" class="img-fluid" style="cursor:pointer;"></a>
                            @else
                                <img id="imgdetails" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="" class="img-fluid" style="cursor:pointer;"></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-xs-12">
                        <div class="info-item d-flex flex-column">
                            <a href="{{ route('product', [$variation, $variation->id]) }}" style="text-decoration:none;color:black;">
                                <h5>{{ $variation->product->name }}</h5>
                            </a>
                            <div class="info-item ">
                                <p class="mb-auto">Product price: <span class="purchPriceID">{{ $variation->price }} €</span></p>
                            </div><br>
                            <div class="separatebtns mt-auto d-flex">
                                @if($variation->stock > 0)
                                    <a id="greenbtn" class="btn btn-outline-dark" onclick="return addToShoppingCart(this, {{ $variation->id }})">Add to Cart</a>
                                @else
                                    <a id="nostock" style="margin-right: 10px;" class="btn btn-outline-dark" >Out of stock</a>
                                @endif
                                    <a id="redbtn" class="btn btn-outline-dark" onclick="return deleteProduct(this, {{ $variation->id }})">Remove</a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <br>
            @endforeach
        </div>
    </div>
</div>

@endsection