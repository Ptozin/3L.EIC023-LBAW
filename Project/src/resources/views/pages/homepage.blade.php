@extends('layouts.app')

@section('title', 'SportsVerse - HomePage')

@section('content')



<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">SportsVerse</h1>
            <p class="lead fw-normal text-white-50 mb-0">The only place you need for a healthy and active lifestyle</p>
        </div>
    </div>
</header>

<section class="mx-5">
    
    <div id="newSearchCont"class="bg-background-off pt-5 pb-3 px-5 mt-2 mb-2 rounded d-flex gap-5">
        <h3 class=" font-bold text-md lg:text-xl pb-4">Search for our products</h3>
        <div id="search-container-w-50">
            <form action="/search" method="GET">
                <input type="text" placeholder="Search.." name="keyword">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>

    <hr>

    <div class="prodListDivHome mx-5 mb-5">
        <div class="bg-background-off p-6 my-3 rounded">
            <h3 class=" font-bold text-md lg:text-xl pb-4">Best Rating Products</h3>
        </div>
        <div class="productsListHome">
            <div class="grid-containerProd row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-6 justify-content-center">
                @foreach($bestRatingProductVariations as $productVariation)
                    @if(count($productVariation->product_images) > 0)
                        @php $url = $productVariation->product_images->first()->url; @endphp
                    @endif
                    @php $product = $productVariation->product; @endphp
                    <div class="grid-itemProd col mb-4">
                        <div class="card h-100">
                            <!-- Product image-->
                            <div class="imgcont1 card-footer">
                                <div class="imgcont2 text-center">
                                    <a class="detailslink" href="{{url('/product/'.$productVariation->id)}}">
                                    @if(count($productVariation->product_images) > 0)
                                        @php $url = $productVariation->product_images->first()->url; @endphp
                                        <img id="imgdetails" class="imgdetails card-img-top" src="{{ asset("images/$url") }}" alt="..." />
                                    @else
                                        <img id="imgdetails" class="imgdetails card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
                                    @endif
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Product details-->
                            <div class="card-body p-2">
                                <div class="text-center">
                                    <!-- Product name-->
                                    <h5 class="productname fw-bolder">{{$product->name}}</h5>
                                    <h6 class="shortDesc fw-normal">{{$product->short_description}}</h6>
                                    <!-- Product reviews-->
                                    <div class="d-flex justify-content-center small mb-2">
                                        @php $rating = $product->rating; @endphp
                                        @while($rating >= 0.5)
                                        @if($rating >= 1)
                                        @php $rating--; @endphp
                                        <div class="bi-star-fill text-warning"></div>
                                        @elseif($rating >= 0.5)
                                        @php $rating-=0.5; @endphp
                                        <div class="bi-star-half text-warning"></div>
                                        @endif
                                        @endwhile
                                        <span class="ms-1">({{$product->rating}})</span>
                                    </div>
                                    @if($productVariation->stock > 5)
                                    <h5 class="alert1 text-success">&#10003; IN STOCK</h5>
                                    @elseif($productVariation->stock === 0)
                                    <h5 id="alert2" class="text-danger bi bi-x"> OUT OF STOCK</h5>
                                    @else
                                    <h5 class="alert3 text-warning bi bi-exclamation-triangle"> LAST UNITS</h5>
                                    @endif
                                    <!-- Product price-->
                                    <h6 class="prodprice" >{{$productVariation->price}}€</h6>
                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <hr>

        <div id="categoriesSpace" class="categoriesSpace">
            <div class="bg-background-off p-6 my-3 rounded">
                <div class="forflexing">
                    <h3 class=" font-bold text-md lg:text-xl">Categories</h3>
                    <a href="/category"><h6 class=" font-bold text-md lg:text-xl">[see all categories]</h6></a>
                </div>
            </div>

            <div id="divlinkcat" class="bg-background-off p-6 my-6 rounded homepageCategories">
                @each('partials.category', $categories, 'category')
            </div>
        </div>

        <hr>

        <div class="bg-background-off p-6 my-3 rounded">
            <h3 class=" font-bold text-md lg:text-xl pb-4">Our Own Products</h3>
        </div>
        <div class="productsListHome">
            <div class="grid-containerProd row gx-4 gx-lg-5  justify-content-center">
                @foreach($ownProductVariations as $productVariation)    
                    @php $product = $productVariation->product; @endphp
                    <div id="grid-itemProd" class="col mb-4">
                        <div class="card h-100">
                            <!-- Product image-->
                            <div id="imgcont1" class="card-footer pt-0 border-top-0 bg-transparent">
                                <div id="imgcont2" class="text-center">
                                    <a class="detailslink" href="{{url('/product/'.$productVariation->id)}}">
                                        @if(count($productVariation->product_images) > 0)
                                            @php $url = $productVariation->product_images->first()->url; @endphp
                                            <img class="imgdetails card-img-top" src="{{ asset("images/$url") }}" alt="..." />
                                        @else
                                            <img class="imgdetails card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
                                        @endif
                                        
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Product details-->
                            <div class="card-body p-2">
                                <div id="prodcont" class="text-center">
                                    <!-- Product name-->
                                    <h5 id="prodname" class="fw-bolder">{{$product->name}}</h5>
                                    <h6 id="shortDesc" class="fw-normal">{{$product->short_description}}</h6>
                                    <!-- Product reviews-->
                                    <div class="d-flex justify-content-center small mb-2">
                                        @php $rating = $product->rating; @endphp
                                        @while($rating >= 0.5)
                                        @if($rating >= 1)
                                        @php $rating--; @endphp
                                        <div id="prodstars" class="bi-star-fill text-warning"></div>
                                        @elseif($rating >= 0.5)
                                        @php $rating-=0.5; @endphp
                                        <div class="bi-star-half text-warning"></div>
                                        @endif
                                        @endwhile
                                        <span id="prodrat" class="ms-1">({{$product->rating}})</span>
                                    </div>
                                    @if($productVariation->stock > 5)
                                    <h5 id="alert1" class="text-success">&#10003; IN STOCK</h5>
                                    @elseif($productVariation->stock === 0)
                                    <h5 id="alert2" class="text-danger bi bi-x"> OUT OF STOCK</h5>
                                    @else
                                    <h5 id="alert3" class="text-warning bi bi-exclamation-triangle"> LAST UNITS</h5>
                                    @endif
                                    <!-- Product price-->
                                    <h6 id="prodprice">{{$productVariation->price}}€</h6>
                                
                                </div>
                            </div>
                        
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>



@endsection