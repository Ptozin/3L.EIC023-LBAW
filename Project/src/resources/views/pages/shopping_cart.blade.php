@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src={{ asset('js/shopping_cart.js') }} defer></script>

<a class="backlink" href="{{ url('/homepage') }}">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<div class="bg-background-off p-6 my-6 rounded">
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Your Shopping Cart</h1>
                <p class="lead fw-normal text-white-50 mb-0">Check out when you are ready</p>
            </div>
        </div>
    </header>

    <div class="container px-4 px-lg-5 mt-5">
        <div class="d-flex">
            <h1>My Products</h1>
        </div><br>
        @foreach(Auth::user()->notifications()->get() as $notification)
            @if($notification->notification_type == 'Price change')    
                @php $notify_price = 1; 
                $not_price_type = $notification->notification_type; 
                @endphp
            @elseif($notification->notification_type == 'Product out of stock')
                @php $notify_stock = 1; 
                $not_stock_type = $notification->notification_type;  
                @endphp
            @endif
        @endforeach
        @if(isset($notify_price) || isset($notify_stock))
            <div class="notifications">
                @if(isset($notify_price))
                <form method="POST" action="{{ route('notification', $not_price_type) }}">
                    @csrf {{ csrf_field() }} 
                    <h3>
                        One of the products in your cart update the price!
                    </h3>
                    <input id="checkoutbtn" class="btn btn-dark btn-primary btn-lg btn-block" value="Confirm" type="submit">
                </form>
                @endif
                @if(isset($notify_stock))
                    <form method="POST" action="{{ route('notification',  $not_stock_type) }}">
                    @csrf {{ csrf_field() }} 
                    <h3>
                    One of the products in your cart is now out of stock! 
                    </h3>
                    <input id="checkoutbtn" class="btn btn-dark btn-primary btn-lg btn-block" value="Confirm" type="submit">
                    </form>                
                @endif
            </div>
        @endif
        <div class="section-container">
            @foreach($products as $product)
                <div class="shopping-cart-item row">
                    <div id="littleimg"class="col-xl-2 col-lg-2 col-md-3 col-sm-12 col-xs-12 pb-lg-0 pb-md-0 pb-sm-3">
                        <div class="d-flex flex-column align-items-center shopping_cart_item_img">
                        <a href="{{ route('product', [$product, $product->id]) }}">
                                @if(count($product->product_images) > 0)
                                    @php $url = $product->product_images->first()->url; @endphp
                                    <img src="{{ asset("images/$url") }}" alt="" class="img-fluid" style="cursor:pointer;"></a>
                                @else
                                    <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="" class="img-fluid" style="cursor:pointer;"></a>
                                @endif
                            <div id="removebtndiv" class="d-flex">
                                <a class="remove-item-cart mr-auto mt-auto btn btn-light" onclick="return deleteProduct(this, {{ $product->id }})"><i style="font-size:24px" class="fa">&#xf014;</i></a>
                            </div>
                        </div>
                    </div>
                    <div id="prodspecif" class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-xs-12">
                        <div class="info-item d-flex flex-column">
                            <a href="{{ route('product', [$product, $product->id]) }}">
                                <h5>{{ $product->product->name }}</h5>
                            </a>
                            <div class="d-flex flex-row shopping-cart-item-quantity">
                                <p>Quantity:&nbsp&nbsp</p>
                                    <p><i onclick="decrement(this)" class="fa fa-minus"></i>&nbsp&nbsp</p>
                                    <input class="numberinput" type="number" class="item_quantity" value="{{ $product->pivot->quantity }}" data-value="{{ $product->stock }}" data-id="{{ $product->id }}">
                                    <p>&nbsp&nbsp<i onclick="increment(this, {{ $product->stock }})" class="fa fa-plus"></i></p>
                            </div>
                            <div class="info-item ">
                                    <p class="mb-auto">Product price: <p class="pricevalue">  {{ $product->price }} €</p></p>
                                    
                            </div>
                            
                        </div>
                    </div>
                </div>
                <hr class="my-4">
            @endforeach
        
            <div id="end_shopping_cart" class="col-12 d-flex justify-content-center">
                <p class="total">Total price:&nbsp</p>
                <p class="total_price">{{ $total }} €</p>
            </div>
            <hr class="my-4">

            <div class="groupboth">

                <img class="about_img" src="https://pbs.twimg.com/profile_images/1275525344834007041/E9XvfsD1_400x400.jpg" alt="no access to the image" />

                <div id="payment_method" class="payment_method col-12 d-flex justify-content-start">
                    <form method="POST" action="{{ route('checkout') }}">
                        <header>Checkout Info</header>
                        @csrf <!-- {{ csrf_field() }} -->

                        <div class="input-area">
                            <label for="pmethod">Payment Method:<label>
                            <input class="debitbtn" type="radio" id="pmethod" name="payment_method" value="debit" required> Debit&nbsp&nbsp&nbsp&nbsp&nbsp
                            <input type="radio" id="pmethod" name="payment_method" value="credit"> Credit
                        </div>
                        <div class="row">
                            <div class="input-area">
                                <label for="cname">Name on Card</label>
                                <input type="text" id="cname" name="cardname" placeholder="Name" required>
                            </div>
                            <div class="input-area">
                                <label for="ccnum">Card Number</label>
                                <input type="text" id="ccnum" name="cardnumber" pattern="\d{16}" placeholder="1111222233334444" required>
                            </div>
                            <br>
                            <div class="input-area">
                                <label for="expmonth">Expiration Date</label>
                                <input type="month" id="expmonth" name="expirationdate" placeholder="mm/yy" required>
                            </div>
                            <div class="input-area">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" pattern="\d{3}" placeholder="123" required>
                            </div>
                        </div>
                        <br>
                
                        <input id="checkoutbtn" class="btn btn-dark btn-primary btn-lg btn-block" value="Checkout" data-toggle="modal" data-target="#checkoutModal" type="submit">
                    </form>
                </div>

            </div>
        </div>

    </div>
<div>
    
@endsection