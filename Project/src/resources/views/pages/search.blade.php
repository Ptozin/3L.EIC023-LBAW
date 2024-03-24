@extends('layouts.app')

@section('title', 'SportsVerse - Search Results')

@section('content')

<a class="backlink" href="/homepage">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<div class="bg-background-off p-6 my-6 rounded">
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Search Our Products</h1>
                <p class="lead fw-normal text-white-50 mb-0">We found these products for you</p>
            </div>
        </div>
    </header>

    <div class="container px-4 px-lg-5 mt-5">
        <div class="products bg-background-off p-6 my-6 rounded">
                <h3>Search results for: "{{ $keyword }}"</h3>
                <span>{{ $products->total() }} Products</span>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        @include('partials.pagination')
                    </ul>
                </nav>
        </div>
        <hr>
        @if($products->total() == 0)
            <span>Sorry, no products found.</span>
        @else
        
        <div id="grid-containerProd" class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-6 justify-content-center">
        @foreach($products as $product)
            <div id="grid-itemProd" class="col mb-4">
                <div class="card h-100">
                    <!-- Product image-->
                    <div id="imgcont1" class="card-footer pt-0 border-top-0 bg-transparent">
                        <div id="imgcont2" class="text-center">
                            <a class="detailslink" href="{{url('/product/'.$product->product_variations->first()->id)}}">
                                @if(count($product->product_variations->first()->product_images) > 0)
                                    @php $url = $product->product_variations->first()->product_images->first()->url; @endphp
                                    <img class="card-img-top" src="{{ asset("images/$url") }}" alt="..." />
                                @else
                                    <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
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
                            @if($product->product_variations->first()->stock > 5)
                            <h5 id="alert1" class="text-success">&#10003; IN STOCK</h5>
                            @elseif($product->product_variations->first()->stock === 0)
                            <h5 id="alert2" class="text-danger bi bi-x"> OUT OF STOCK</h5>
                            @else
                            <h5 id="alert3" class="text-warning bi bi-exclamation-triangle"> LAST UNITS</h5>
                            @endif
                            <!-- Product price-->
                            <h6 id="prodprice">{{$product->product_variations->first()->price}}€</h6>
                        
                        </div>
                    </div>
                
                </div>
            </div>


            
        @endforeach
        </div>
        @endif
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @include('partials.pagination')
            </ul>
        </nav>
    </div>
</div>

@endsection