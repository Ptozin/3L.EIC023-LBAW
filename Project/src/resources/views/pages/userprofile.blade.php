@extends('layouts.app')

@section('title', 'SportsVerse - User Profile')

@section('content')


<script src={{ asset('js/user.js') }} defer></script>
<script src={{ asset('js/eventListeners.js') }} defer></script>


<a class="backlink" href="{{ url('/homepage') }}">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">{{$user->name}}</h1>
            <p class="lead fw-normal text-white-50 mb-0">User Profile</p>
        </div>
    </div>
</header>


<main class="all">
<p class="msg1"></p>
<br>
<p class="msg2"></p>
    <div class="row g-5" id="userinformation">
        <div class="col-md-8">
            <div id="showInformationProfile">
                <div id="selectInformation">
                    <button id="histPurchBtn" onclick="updateUserInfo('userPurchases', {{$user->id}})">Purchases</button>
                    {{-- <button onclick="updateUserInfo('wishlist', {{$user->id}})">Wishlist</button> --}}
                    <button id="histRevBtn" onclick="updateUserInfo('userReviews', {{$user->id}})">Reviews</button>
                </div>
                <div class="info" id="userinfo">
                    <ul>
                        <div class="purchase_history_container" id="purchasesList">
                            <h5 class="boxtitle">Purchase History</h5>
                            @foreach($user->purchases as $purchase)
                            <article class="purchaseUserProfile pe-4">
                                <header>
                                    <div class="purchDate"> 
                                        <p>Date</p>
                                        <p class="purchDateID">{{$purchase->date}}</p>
                                    </div>
                                    <p class="purchPrice" >Price - <span class="purchPriceID">{{$purchase->price}} €</span></p>
                                    <p class="cheating">.</p>
                                    @if(Auth::user()->is_admin == False)
                                    <p class="purchStatus" id="currentStatus">Status - <span class="purchStatusID"> {{$purchase->pur_status}}</span></p>
                                    <p class="expand"><button class="expandProds" >Show more</button></p>
                                    
                                    @if(Auth::user()->is_admin == False && $purchase->pur_status != "Concluded" && $purchase->pur_status != "Canceled")
                                    <div class="text-not-center popup" id="cancelOrderContainer">
                                        <a class="btn btn-outline-dark" id="cancelOrderButton" onClick="cancelOrder()" purchaseid="{{ $purchase->id }}">Cancel Order</a>
                                    </div>
                                    @endif

                                    @else
                                    <p>Status: </p>
                                    <select id="changeStatus" name="orderStatus" purchaseid="{{ $purchase->id }}" userid="{{ $user->id }}" >
                                    <option value="" disabled selected>{{ $purchase->pur_status }}</option>
                                    <option value="Payment Pending">Payment Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Shipping">Shipping</option>
                                    <option value="Canceled">Canceled</option>
                                    <option value="Concluded">Concluded</option>
                                    </select>
                                    @endif
                                </header>
                                @foreach($purchase->product_purchase as $product_purchase)
                                <hr class="stay">
                                <section class="productUserProfile" id="productsPurchaseList">
                                    <div class="cart-item row">
                                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm col-xs" id="todeletepadd">
                                            <div class="d-flex flex-column align-items-center cart_item_img">
                                            @if(count($product_purchase->product_images) > 0)
                                                @php $url = $product_purchase->product_images->first()->url; @endphp
                                                <img id="imgdetails" src="{{ asset("images/$url") }}" alt="" class="img-fluid" style="cursor:pointer;">
                                            @else
                                                <img id="imgdetails" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="" class="img-fluid card-img-top" style="cursor:pointer;">
                                            @endif
                                            </div>
                                        </div>
                                        <div class="col-xl-10 col-lg-10 col-md-9 col-sm-12 col-xs-12">
                                            <div class="info-item d-flex flex-column">
                                                <span class="pe-4"><small>{{$product_purchase->product->name}}</small></span>
                                                <div class="info-item ">
                                                    <span class="purchPriceID pe-4"><small>{{$product_purchase->price}} €</small></span>
                                                    <span><small>(x {{$product_purchase->pivot->quantity}})</small></span>
                                                </div>
                                                <div class="separatebtns mt-auto d-flex">
                                                    <div class="text-center pe-4"><a class="btn btn-outline-dark" href="{!! route('product', $product_purchase['id']) !!}">Check Product</a></div>
                                                    <div class="text-center pe"><a class="btn btn-outline-dark" href="{!! route('review', $product_purchase['id']) !!}">Review Product</a></div>
                                                    <br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                @endforeach
                                @if (!$loop->last)
                                <hr>
                                @endif
                            </article>
                            @endforeach
                        </div>

                        <div class="reviewsContainer" id="reviewsList" hidden>
                            <h2 class="boxtitle">Review History</h2>
                            <div class="userReviews">
                                @foreach($user->reviews as $review)
                                    <article class="reviewUserProfile pe-4">
                                        <header>
                                            <hr>
                                            <p class="productReview"><a class="fw-bolder listingAdminProfile" href="{{url('/product/'.$review->product->product_variations->first()->id)}}">{{$review->product->name}}</a></p>
                                            <hr>
                                            <div class="purchDate"> 
                                                <p>Date</p>
                                                <p id="purchDateID">{{$review->date}}</p>
                                            </div>
                                            <div class =reviewStuff>
                                                <p class="reviewRating">
                                                    <div class="reviewRatingStars">
                                                        <span id="reviewRating">{{$review->rating}}</span>
                                                        @php $rating = $review->rating; @endphp
                                                        @while($rating >= 0.5)
                                                        @if($rating >= 1)
                                                        @php $rating--; @endphp
                                                        <div id="prodstars" class="bi-star-fill text-warning"></div>
                                                        @elseif($rating >= 0.5)
                                                        @php $rating-=0.5; @endphp
                                                        <div class="bi-star-half text-warning"></div>
                                                        @endif
                                                        @endwhile
                                                    </div>
                                                </p>
                                                <p class="reviewComment">Comment: <span id="reviewID">{{$review->comment}}</span></p>
                                            </div>
                                            @if(Auth::user()->is_admin)
                                                <form method="POST" action="{!! route('delete_review', [$review->user_id, $review->id_product]) !!}">
                                                    {{ csrf_field() }}
                                                    <div class="deleteReview">
                                                        <input class="delReviewbtn" type="submit" value="Delete Review"></input>
                                                    </div> 
                                                </form>
                                            @else
                                                <form method="GET" action="{!! route('review', $review->id_product) !!}">
                                                    {{ csrf_field() }}    
                                                    <div class="editReview">
                                                        <input class="editReviewbtn" type="submit" value="Edit Review"></input>
                                                    </div> 
                                                </form>
                                            @endif
                                            
                                        </header>
                                        <hr>  
                                    </article>
                                @endforeach
                            </div>
                        </div>

                    </ul>
                </div>
            </div>
            
    </div>

    <div class="col-md-4">
        <div class="position-sticky">
            <h2 id="first_hide" >Profile Information</h2>
            <div class="p-4 mb-3 bg-light rounded">
                <img class="profile_img" src="https://helsinkisailing.com/wp-content/uploads/2020/01/profile-pic.png" alt="no access to the image" />
            </div>

            <div class="editdiv">
                <div id="uinfo">
                    <p>Name: <span class="fw-bolder">{{$user->name}}</span></p>
                    <p>Email: <span class="fw-bolder">{{$user->email}}</span></p>
                    <p class="hidep">Address: <span class="fw-bolder">{{$user->address}}</span></p>
                    <p class="hidep">Birthdate: <span class="fw-bolder">{{$user->birthdate}}</span></p>
                    <p class="hidep">Phone number: <span class="fw-bolder">{{$user->phone_number}}</span></p>
                </div>
                <div class="managementbuttons">
                    @if($user == Auth::user())
                    <a class="managementbutton" id="editprof" href="{{url('/profile/edit')}}">Edit Profile</a>
                    @else
                    <a class="managementbutton" id="editprof" href="{{url('/user/')}}/{{$user->id}}/edit">Edit Profile</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</main>


@endsection