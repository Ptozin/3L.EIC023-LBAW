<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="{{ url('/homepage') }}">SportsVerse</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="{{ url('/homepage') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ url('/homepage') }}">All Products</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="{{ url('/homepage#categoriesSpace') }}">By Categories</a></li>
                        <li><a class="dropdown-item" href="#!">Popular Items</a></li>
                    </ul>                        
                </li>
            </ul>
            
            <div id="search-container">
                    <form action="/search" method="GET">
                        <input type="text" placeholder="Search.." name="keyword">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
            </div>
            @if (Auth::check())
                @if (!Auth::user()->is_admin)
                    @php $wish_size = count(Auth::user()->wishlist()->get()); @endphp
                    @foreach(Auth::user()->notifications()->get() as $notification)
                        @if($notification->notification_type == 'Product available')    
                            @php $notify_wish = 1; @endphp
                        @else
                            @php $notify_cart = 1; @endphp
                        @endif
                    @endforeach
                    <form id="wishlistform" class="d-flex" method="GET" action="{{ route('wishlist') }}">                
                        <button class="btn btn-outline-dark" id="wishlistbut" type="submit">
                            <span>
                                <div><i class='fa fa-heart'></i></div>
                                <div class="text4emoji">Wishlist</div>
                            </span>
                            
                            @if (isset($notify_wish))
                                    <span class="badge doblink">!</span>
                            @else
                                @if ($wish_size > 9)
                                    <span class="badge">9+</span>
                                @elseif ($wish_size > 0)
                                    <span class="badge">{{$wish_size}}</span>
                                @endif
                            @endif
                        </button>
                    </form>
                    @php $cart_size = count(Auth::user()->shopping_cart()->get()); @endphp
                    <form id="cartform" class="d-flex" method="GET" action="{{ route('shopping_cart') }}">                
                        <button class="btn btn-outline-dark" id="cartbut" type="submit">
                            <span>
                                <div><i class="bi-cart-fill me-1"></i></div>
                                <div class="text4emoji">Cart</div>
                            </span>
                            @if (isset($notify_cart))
                                    <span class="badge doblink">!</span>
                            @else
                                @if ($cart_size > 9)
                                    <span class="badge">9+</span>
                                @elseif ($cart_size > 0)
                                    <span class="badge">{{$cart_size}}</span>
                                @endif
                            @endif
                        </button>
                    </form>
                @endif
            @else 
                <form id="wishlistform" class="d-flex" method="GET" action="{{ route('wishlist') }}">                
                    <button class="btn btn-outline-dark" id="wishlistbut" type="submit">
                        <div><i class='fa fa-heart'></i></div>
                        <div class="text4emoji">Wishlist</div>
                    </button>
                </form>

                <form id="cartform" class="d-flex" method="GET" action="{{ route('shopping_cart') }}">                
                    <button class="btn btn-outline-dark" id="cartbut" type="submit">
                        <span>
                            <div><i class="bi-cart-fill me-1"></i></div>
                            <div class="text4emoji">Cart</div>
                        </span>
                        <span class="badge">?</span>
                    </button>
                </form>
            @endif

            <div class="userbuts" id="centerit">
                @if (Auth::check())
                    <a class="btn btn-outline-dark" id="username" href="{{ url('/profile') }}">{{ Auth::user()->name }}</a>
                    <a class="btn btn-outline-dark" id="log_but" href="{{ url('/logout') }}"> Logout </a> 
                @else
                    <a class="btn btn-outline-dark" id="log_but" href="{{ url('/login') }}"> Login </a> 
                @endif
            </div>
            

        </div>
    </div>
  </nav>

  

  