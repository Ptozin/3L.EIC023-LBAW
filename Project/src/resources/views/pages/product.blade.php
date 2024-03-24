@extends('layouts.app')

@section('title', 'SportsVerse - Product Page')

@section('content')

<a class="backlink dark" href="/homepage">
    <i id="leftarrow" class='fa fa-arrow-left' ></i>
</a>

<div class="product-container"> 
    <!-- Left Column-->
    <div class="left-column">
        @if (count($variation->product_images) > 0)
            @foreach($variation->product_images as $images)
                @if($loop->first)
                    <img class="active" data-image="{{$images->id}}" src="{{ asset("images/$images->url") }}" alt="image">
                @else
                    <img data-image="{{$images->id}}" src="{{ asset("images/$images->url") }}" alt="image">
                @endif
            @endforeach
        @else
            <img class="active" data-image="data_image" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="image">
        @endif 
    </div>

    <!-- Right Column -->
    <div class="right-column">

        <!-- Product Description -->
        <div class="product-description">
            <div class = "description-header">
                <span class="category"><a style="text-decoration: none;" href="{!! route('subcategory', $variation->product->sub_category['id']) !!}">{{$variation->product->sub_category->name}}</a></span>                                
                <span class="rating-right">
                    @php $rating = $variation->product->rating; @endphp
                    @while($rating >= 0.5)
                        @if($rating >= 1)
                            @php $rating--; @endphp
                            <div class="bi-star-fill text-warning"></div>
                        @elseif($rating >= 0.5)
                        @php $rating-=0.5; @endphp
                            <div class="bi-star-half text-warning"></div>
                        @endif
                    @endwhile
                    {{$variation->product->rating}}
                </span>
            </div>
            <h1 class="prodname">{{$variation->product->name}}</h1>
            <p>{{$variation->product->short_description}}</p>
            @if($variation->color->id != 1)
                <li>Color: {{$variation->color->color}}</li>
            @endif
            @if($variation->size->id != 1)
            <li>Size: {{$variation->size->size}}</li>
            @endif
        </div>

        <!-- Product Configuration -->
        <div class="product-configuration">

            <!-- Product Images -->
            <div class="product-color">
            <span>Images</span>

            <div class="color-choose">
                @if (count($variation->product_images) > 0)
                    @foreach($variation->product_images as $images)
                        @if($loop->first)
                            <img class="selected" data-image="{{$images->id}}" src="{{ asset("images/$images->url") }}" alt="product image">
                        @else
                            <img data-image="{{$images->id}}" src="{{ asset("images/$images->url") }}" alt="product image">
                        @endif
                    @endforeach
                @else
                    <img class="selected" data-image="data_image" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="product image">
                @endif 
            </div>

            </div>

            <!-- Variation Configuration -->
            <div class="cable-config">
            <span>Variations</span>

            <div class="cable-choose">
                @foreach($variation->product->product_variations as $var)
                    @if($var->id == $variation->id)
                        <span style="border: 2px solid #86939E;" class= "product_variation" type="product_variation" name="product_variation" value="{{$var->id}}">{{$var->id}}</span>
                    @else
                        <a class= "product_variation" type="product_variation" name="product_variation" value="{{$var->id}}" href="{!! route('product', $var['id']) !!}">{{$var->id}}</a>
                    @endif
                @endforeach
            </div>

            </div>
        </div>
        @if(Auth::user())
            @if(Auth::user()->is_admin)
            <button class="btn btn-dark"><a id="editprod" style="text-decoration: none; color: white" href="{{url('/product/')}}/{{$variation->id}}/edit">Edit Product</a></button>
            <button class="btn btn-dark"><a id="addprod" style="text-decoration: none; color: white" href="{{url('/product/')}}/{{$variation->id}}/addvariation">Add Product</a></button>
            @else
                <!-- Product Pricing -->
                <div class="product-price">
                    <span>{{$variation->price}} €</span>
                    <button type="submit" class="wish-btn" onclick="return addToWishlist({{$variation->id}})">&#10084; Wishlist</button>
                </div>
                <div class="product-cart">
                    <div class="quantity-input">                        
                        <span class="minus_quantity">-</span>
                        <span id="{{$variation->stock}}" class="quantity_val" type="text" name="product-quantity" data-max="120" pattern="[0-9]*" >1</span>
                        <span class="plus_quantity">+</span>  
                    </div>
                    <div class ="cart-wish"> 
                        <button type="submit" class="cart-btn" onclick="return addToShoppingCart(null, {{$variation->id}})">Add to Cart</button>
                    </div>
                </div>
            @endif
        @else
            <!-- Product Pricing -->
            <div class="product-price">
                <span>{{$variation->price}} €</span>
                <button type="submit" class="wish-btn" onclick="return addToWishlist({{$variation->id}})">&#10084; Wishlist</button>
            </div>
            <div class="product-cart">
                <div class="quantity-input">                        
                    <span class="minus_quantity">-</span>
                    <span id="{{$variation->stock}}" class="quantity_val" type="text" name="product-quantity" data-max="120" pattern="[0-9]*" >1</span>
                    <span class="plus_quantity">+</span>  
                </div>
                <div class ="cart-wish"> 
                    <button type="submit" class="cart-btn" onclick="return addToShoppingCart(null, {{$variation->id}})">Add to Cart</button>
                </div>
            </div>
        @endif
    </div>
</div>
<div class="description-review-container">
    <div class="bottom-column">
        <div class="tab-control">
            <a id="description-id" href="#description" class="activated">Description</a>
            <a id="review-id" href="#review">Reviews</a>
        </div>
        <div class="tab-contents">
            <span style="opacity: 0;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
            <div class="selected" data-image="description-id">
                <div class="long-description">
                    <p>{{$variation->product->manufacturer}}</p>
                    <p>{{$variation->product->long_description}}</p>
                </div>
            </div>
            <div data-image="review-id">
                @if (count($variation->product->reviews) > 0)
                    @foreach($variation->product->reviews as $review)
                        <div class ="review">
                            <div class="review-header">
                                <span class ="name">{{$review->user->name}}</span>
                                <span class ="date">{{$review->date}}</span>
                            </div>
                            <div class = "user-rating">
                                <span class ="rating">
                                {{$review->rating}}.0 
                                    @php $user_rating = $review->rating; @endphp
                                    @while($user_rating >= 0.5)
                                        @if($user_rating >= 1)
                                            @php $user_rating--; @endphp
                                            <div class="bi-star-fill text-warning"></div>
                                        @elseif($user_rating >= 0.5)
                                        @php $user_rating-=0.5; @endphp
                                            <div class="bi-star-half text-warning"></div>
                                        @endif
                                    @endwhile
                                </span>
                            </div>
                            <p class ="comment">{{$review->comment}}</p>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        </div> 
                    @endforeach
                @else
                <div class ="review">  
                    <p class ="comment">This product has no reviews</p>
                </div> 
                @endif        
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
 
        $('.color-choose img').on('click', function() {
            var image = $(this).attr('data-image');
            $('.color-choose .selected').removeClass('selected');
            $('.active').removeClass('active');
            $('.left-column img[data-image = ' + image + ']').addClass('active');
            $('.color-choose img[data-image = ' + image + ']').addClass('selected');
            $(this).addClass('active');
        });

    });
</script>
<script>
    $(document).ready(function() {
    
        $('.tab-control a').on('click', function() {
            var selection = $(this).attr('id');
            $('.activated').removeClass('activated');
            $('.tab-contents .selected').removeClass('selected');
            $('.tab-control a[id = ' + selection + ']').addClass('activated');
            $('.tab-contents div[data-image = ' + selection + ']').addClass('selected');
            $(this).addClass('active');
        });

    });
</script>
<script>
    const plus = document.querySelector(".plus_quantity"),
          minus = document.querySelector(".minus_quantity"),
          maximum = document.querySelector(".quantity_val").id;
          quantity = document.querySelector(".quantity_val");
    let a = 1;
    plus.addEventListener("click", ()=> {
        if(a+1 > maximum)
            ;
        else {
            a++;
            quantity.innerText = a;
            plus.innerText = "+";
            minus.innerText = "-";
        }
    });
    minus.addEventListener("click", ()=> {
        if(a-1 < 1)
            ;
        else {
            a--;
            quantity.innerText = a;
            plus.innerText = "+";
            minus.innerText = "-";
        }
    });
</script>
<script src={{ asset('js/products.js') }} defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>
@endsection