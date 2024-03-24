<script src={{ asset('js/products.js') }} defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>

<div class="product_container">
    {{ csrf_field() }} 
    <div class="wrap">
        <div class="wrap-product-details">
            <div class="another-wrap">
                <div class="product-media">              
                    <div class="product-gallery">
                        <div class ="btn-slider">
                            <button class="btn-slider-left" onclick="prev()">&#60;</button>
                            <button class="btn-slider-right" onclick="next()">&#62;</button>
                        </div>
                        <ul class="gallery-slides">
                            @if (count($variation->product_images) > 0)
                                @foreach($variation->product_images as $images)
                                    <div class="image-box">
                                        <img src={{$images->url}} alt="product thumbnail" />
                                    </div>
                                @endforeach
                            @else
                                <div class="image-box">
                                    <span>image placeholder TODO</span>
                                </div>
                            @endif 
                        </ul>
                    </div>   
                                
                </div>
                <div class="product-info">
                    <div class="general-info">
                        <ul class="product-related">
                            <div>
                                <ul class="product_stars">
                                    <span class="stars" type="radio" id="star1">&#9733</span>
                                    <span class="stars" type="radio" id="star2">&#9733</span>
                                    <span class="stars" type="radio" id="star3">&#9733</span>
                                    <span class="stars" type="radio" id="star4">&#9733</span>
                                    <span class="stars" type="radio" id="star5">&#9734</span>
                                    <span class="product_rating">{{$variation->product->rating}}</span>
                                </ul>
                                
                            </div>
                            <li>Name: <span class="product_name">{{$variation->product->name}}</span></li>
                            <hr class ="product_hr">
                            <div class="product_atributes">
                                <li class="product_short">{{$variation->product->short_description}}</li>
                                <li class="product_color">Color: {{$variation->color->color}} </li> 
                                <li class="product_size">Size: {{$variation->size->size}} </li> 
                            </div>
                            <li class="product_price">{{$variation->price}} €</li>    
                            <li class="quantity">Quantity:</li >
                            <div class="product_quantity" data-id='1'>
                                <div class="quantity_input">
                                    
                                    <span class="minus_quantity">-</span>
                                    <span class="quantity_val" type="text" name="product-quantity" data-max="120" pattern="[0-9]*" >1</span>
                                    <span class="plus_quantity">+</span>  
                                </div>
                                <div class = "add_to_cart">
                                    <button type="submit" onclick="return addToShoppingCart(null, {{$variation->id}})">Add to Cart </button>
                                </div>   
                            </div>
                            <div class="product_stock">
                                <span>Products in stock: </span>
                                <span id="product_stock">{{$variation->stock}}</span>
                            </div>
                        </ul>
                    </div>
                    <div class = "variation-info">
                        <ul class="variations-slides">
                            @foreach($variation->product->product_variations as $var)
                                @if($var->id == $variation->id)
                                    
                                    <li><span style="background-color: rgb(199, 34, 43);" class= "product_variation" type="product_variation" name="product_variation" value="{{$var->id}}">{{$var->id}}</span></li>
                                @else
                                    <li><a class= "product_variation" type="product_variation" name="product_variation" value="{{$var->id}}" href="{!! route('product', $var['id']) !!}">{{$var->id}}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class = "big-info">
            <div class ="info_headers">
                <div class="description_header">
                    <span id="description_header">Description</span>
                </div>
                <div class="reviews_header">
                    <span id="reviews_header">Reviews</span>
                </div>
            </div>
            <div class="info_content">
                <div class="info_description">
                    <li>Manufacturer: <span class="product_manufacturer">{{$variation->product->manufacturer}}</span></li>
                    <li>Details: <span class="product_description">{{$variation->product->long_description}}</span></li>
                </div>
                <div class="info_review">
                    @if (count($variation->product->reviews) > 0)
                        @foreach($variation->product->reviews as $review)
                            <div class="review_header">
                                <div class="user_name">
                                    <li id="user_name">{{$review->user->name}}</li>
                                </div>
                                <div class="review_date">
                                    <li id="review_date">{{$review->date}}</li>
                                </div>
                            </div>
                            <div class="review_body">
                                <div class="user_rating">
                                    <span>Rating: <span id="user_rating">{{$review->rating}}&#9733</span></span>                                
                                </div>
                                <div class="user_review">
                                    <li id="user_review">{{$review->comment}}</li>
                                </div>
                            </div>
                            @if (!$loop->last)
                                <hr class ="product_hr">
                            @endif
                        @endforeach
                    @else
                        <div class="review_header">
                            <div class="user_name">
                                <li id="user_name">There are no reviews yet</li>
                            </div>
                        </div>
                        <div class="review_body">
                            <div class="user_review">
                                <li id="user_review">Be the first one to rate it!</li>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const plus = document.querySelector(".plus_quantity"),
          minus = document.querySelector(".minus_quantity"),
          maximum = document.querySelector("#product_stock").textContent,
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
<script>
    var sliderimg = document.querySelector('.product-gallery');
    var images = document.querySelectorAll('.image-box');
    var i = 0;

    function prev() {
        if(i <= 0) i = images.length;
        i--;
        return setImg();
    }
    function next() {
        if(i >= images.length-1) i = -1;
        i++;
        return setImg();
    }
    function setImg() {
        return sliderimg.setAttribute('src', 'images/' + images[i]);
    }
</script>
