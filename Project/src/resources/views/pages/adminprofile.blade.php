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
            <p class="lead fw-normal text-white-50 mb-0">Admin Profile</p>
        </div>
    </div>
</header>


<main class="all">
    <div class="row g-5" id="userinformation">
        <div class="col-md-8">
            <div id="showInformationProfile">
                <div id="selectInformation">
                    <button id="adminHistUsersBtn" onclick="updateAdminInfo('users')">Users</button>
                    <button id="adminHistRevBtn" onclick="updateAdminInfo('reviews')">Reviews</button>
                    <button id="adminHistPurchBtn" onclick="updateAdminInfo('purchases')">Purchases</button>
                </div>
                <div class="info" id="admininfo">
                    <ul>
                        <div class="usersContainer" id="adminUsersList">
                            <h2 class="boxtitle">Users</h2>
                            @foreach($userList as $listedUser)
                            <ul>
                            @if($listedUser->blocked)
                            <li><a class="fw-bolder listingAdminProfile text-danger" href="{{url('/user/'.$listedUser->id)}}">{{$listedUser->name}} - BLOCKED</a></li>
                            @else
                            <li><a class="fw-bolder listingAdminProfile" href="{{url('/user/'.$listedUser->id)}}">{{$listedUser->name}} </a></li>
                            @endif
                            </ul>
                            @endforeach
                        </div>

                        <div class="reviewsContainer" id="adminReviewsList" hidden>
                            <h2 id="boxtitle">Reviews</h2>
                            <div class="userReviews">
                                @foreach($reviews as $review)
                                    <article class="reviewUserProfile pe-4">
                                        <header>
                                            <hr>
                                            @if($review->user->blocked)
                                                <p class="user_name"><a class="fw-bolder listingAdminProfile text-danger" href="{{url('/user/'.$review->user->id)}}">{{$review->user->name}} id: {{$listedUser->id}} BLOCKED</a></p>
                                            @else
                                                <p class="user_name"><a class="fw-bolder listingAdminProfile" href="{{url('/user/'.$review->user->id)}}">{{$review->user->name}}</a></p>
                                            @endif
                                            <hr>
                                            <div class="purchDate"> 
                                                <p>Date</p>
                                                <p id="purchDateID">{{$review->date}}</p>
                                            </div>
                                            <div class =reviewStuff>
                                                <p class="reviewProduct">Product: <a class="fw-bolder listingAdminProfile" href="{{url('/product/'.$review->product->product_variations->first()->id)}}">{{$review->product->name}}</a></p>
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
                                            <form method="POST" action="{!! route('delete_review', [$review->user_id, $review->id_product]) !!}">
                                                {{ csrf_field() }}
                                                <div class="deleteReview">
                                                    <input class="delReviewbtn" type="submit" value="Delete Review"></input>
                                                </div> 
                                            </form>
                                        </header>
                                        <hr>  
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="purchasesContainer" id="adminPurchasesList" hidden>
                            <h2 class="boxtitle">Purchases</h2>
                            @foreach($userList as $listedUser)
                            @foreach($listedUser->purchases as $purchase)
                            <article class="purchaseUserProfile pe-4">
                                <header>
                                    <hr>
                                    @if($listedUser->blocked)
                                    <p class="user_name"><a class="fw-bolder listingAdminProfile text-danger" href="{{url('/user/'.$listedUser->id)}}">{{$listedUser->name}} id: {{$listedUser->id}} BLOCKED</a></p>
                                    @else
                                    <p class="user_name"><a class="fw-bolder listingAdminProfile" href="{{url('/user/'.$listedUser->id)}}">{{$listedUser->name}}</a></p>
                                    @endif
                                    <hr>
                                    <div class="purchDate"> 
                                        <p>Date</p>
                                        <p class="purchDateID">{{$purchase->date}}</p>
                                    </div>
                                    <p class="purchPrice">Price - <span class="purchPriceID">{{$purchase->price}} €</span></p>
                                    @if(Auth::user()->is_admin == False)
                                    <p class="purchStatus" id="currentStatus">Status <span class="purchStatusID"> {{$purchase->pur_status}}</span></p>
                                    
                                    @if(Auth::user()->is_admin == False && $purchase->pur_status != "Concluded" && $purchase->pur_status != "Canceled")
                                    <div class="text-center popup" id="cancelOrderContainer">
                                        <a class="btn btn-outline-dark" id="cancelOrderButton" onClick="cancelOrder()" purchaseid="{{ $purchase->id }}">Cancel Order</a>
                                    </div>
                                    @endif

                                    @else
                                    <p class="purchStatus" >Status - 
                                    <select id="changeStatus" name="orderStatus" purchaseid="{{ $purchase->id }}" userid="{{ $user->id }}" >
                                    <option value="" disabled selected>{{ $purchase->pur_status }}</option>
                                    <option value="Payment Pending">Payment Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Shipping">Shipping</option>
                                    <option value="Canceled">Canceled</option>
                                    <option value="Concluded">Concluded</option>
                                    </select>
                                    </p>
                                    @endif
                                </header>
                                
                                <hr>
                                
                            </article>
                            @endforeach
                            @endforeach

                        </div>

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="position-sticky">

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
                    @if($user == Auth::user())
                    <div class="managementbuttons">
                        <a class="managementbutton" id="addcat" href="{{url('/category/add')}}">Add Category</a>
                    </div>
                    <div class="managementbuttons">
                        <a class="managementbutton" id="addsubcat" href="{{url('/subcategory/add')}}">Add Subcategory</a>
                    </div>
                    <div class="managementbuttons">
                        <a class="managementbutton" id="addprod" href="{{url('/product/add')}}">Add Product</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>


@endsection