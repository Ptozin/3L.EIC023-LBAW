@extends('layouts.app')

@section('title', 'SportsVerse - Review Form')

@section('content')

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Your opinion matters!</h1>
            <p class="lead fw-normal text-white-50 mb-0">Give your feedback on our products and help others to make the right decision!</p>
        </div>
    </div>
</header>


@if(!empty($review))
    <form class="reviewForm mx-5" method="POST" action="{!! route('edit_review', $product['id']) !!}">
        {{ csrf_field() }}
        <div class="prod4review">

            <div class="shopping-cart-item row">
                <div id="littleimg">
                    <a href="{{ route('product', [$product_variation, $product_variation->id]) }}">
                        @if(count($product_variation->product_images) > 0)
                            @php $url = $product_variation->product_images->first()->url; @endphp
                            <img src="{{ asset("images/$url") }}" alt="" class="img-fluid" style="cursor:pointer;">
                        @else
                            <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="" class="img-fluid" style="cursor:pointer;">
                        @endif
                    </a>
                </div>

                <div id="prodspecif" >
                    <a href="{{ route('product', [$product_variation, $product_variation->id]) }}">
                        <h5>{{ $product->name }}</h5>
                    </a>
                    <p class="mb-auto">Purchase Date: {{$purchase->date}}</p>
                            
                </div>
            </div>
            <div class="makeReview">

                <h3>You have already rated this product</h3>
                
                <div id="rating"> 
                    @if($review->rating == 1)
                        <input type="radio" name="score" checked="checked" value="1">
                    @else
                        <input type="radio" name="score" value="1">
                    @endif
                    <span>1</span>
                    @if($review->rating == 2)
                        <input type="radio" name="score" checked="checked" value="2">
                    @else
                        <input type="radio" name="score" value="2">
                    @endif
                    <span>2</span>
                    @if($review->rating == 3)
                        <input type="radio" name="score" checked="checked" value="3">
                    @else
                        <input type="radio" name="score" value="3">
                    @endif
                    <span>3</span>
                    @if($review->rating == 4)
                        <input type="radio" name="score" checked="checked" value="4">
                    @else
                        <input type="radio" name="score " value="4">
                    @endif
                    <span>4</span>
                    @if($review->rating == 5)
                        <input type="radio" name="score" checked="checked" value="5">
                    @else
                        <input type="radio" name="score" value="5">
                    @endif
                    <span>5</span>
                </div>

                <p>Add a written review:</p>

                <textarea id="comment" class="commentBox" name="comment" required placeholder ="Enter Review...">{{$review->comment}}</textarea>

                <div class ="reviewBtns">
                    <input class="postReviewbtn" name="action" type="submit" value="Submit"></input>
                    <input class="delReviewbtn"  name="action" type="submit" value="Delete Review"></input>
                </div>

                
            </div>
        </div>   
    </form>
@else
    <form class="reviewForm mx-5" method="POST" action="{!! route('add_review', $product['id']) !!}">
        {{ csrf_field() }}
        <div class="prod4review">

            <div class="shopping-cart-item row">
                <div id="littleimg">
                    <a href="{{ route('product', [$product, $product->id]) }}">
                        @if(count($product_variation->product_images) > 0)
                            @php $url = $product_variation->product_images->first()->url; @endphp
                            <img src="{{ asset("images/$url") }}" alt="" class="img-fluid" style="cursor:pointer;">
                        @else
                            <img src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="" class="img-fluid" style="cursor:pointer;">
                        @endif
                    </a>
                </div>

                <div id="prodspecif" >
                    <a href="{{ route('product', [$product, $product->id]) }}">
                        <h5>{{ $product->name }}</h5>
                    </a>
                    <p class="mb-auto">Purchase Date: {{$purchase->date}}</p>
                             
                </div>
            </div>
            <div class="makeReview">
            
                <h3>Rate this product</h3>
                
                <div id="rating"> 
                    <input type="radio" name="score" value="1">
                    <span>1</span>
                    <input type="radio" name="score" value="2">
                    <span>2</span>
                    <input type="radio" name="score" value="3">
                    <span>3</span>
                    <input type="radio" name="score" value="4">
                    <span>4</span>
                    <input type="radio" name="score" value="5" checked>
                    <span>5</span>
                </div>

                <p>Add a written review:</p>

                <textarea id="comment" class="commentBox" name="comment" required placeholder ="Enter Review..."></textarea>
    
                <input class="postReviewbtn" type="submit" value="Submit"></input>

            </div>
        </div>
    </form>
@endif
    
@endsection