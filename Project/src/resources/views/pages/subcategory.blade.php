@extends('layouts.app')

@section('title', 'SportsVerse - Category')

@section('content')

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">{{$subcategory->category->name}}</h1>
            <p id="subcatname" class="lead fw-normal text-white-50 mb-0">{{$subcategory->name}}</p>
        </div>
    </div>
</header>


<section class="py-4">

    <div id="catproddiv" class="prodListDiv">
        <a class="backlink" href="{{ url('/homepage') }}">
            <i id="leftarrow" class='fa fa-arrow-left'></i>
        </a>
        <div id="header">
            <h3 class="pe-1">{{$subcategory->name}} - {{count($productVariations)}} products</h3>
            <div class="ps-1">
                <a class="nav-link dropdown-toggle" id="sortDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Sort By</a>
                <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                    <li><a class="dropdown-item" href="{{url('/products/subcategory/'.$subcategory->id.'?sort=nameasc')}}">Name &#8593;</a></li>
                    <li><a class="dropdown-item" href="{{url('/products/subcategory/'.$subcategory->id.'?sort=namedesc')}}">Name &#8595;</a></li>
                    <li><a class="dropdown-item" href="{{url('/products/subcategory/'.$subcategory->id.'?sort=priceasc')}}">Price &#8593;</a></li>
                    <li><a class="dropdown-item" href="{{url('/products/subcategory/'.$subcategory->id.'?sort=pricedesc')}}">Price &#8595;</a></li>
                    <li><a class="dropdown-item" href="{{url('/products/subcategory/'.$subcategory->id.'?sort=rating')}}">Rating</a></li>
                </ul>
            </div>
            <div class=" ps-1">
                <a class="nav-link dropdown-toggle" id="categoriesDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{$subcategory->category->name}}</a>
                <ul class="dropdown-menu" id="categoriesDropdownUL" aria-labelledby="categoriesDropdown">
                    @foreach($allCategories as $otherCategory)
                    @if($otherCategory->id !== $subcategory->category->id)
                    <li><a class="dropdown-item" href="{{url('/products/category/'.$otherCategory->id)}}">{{$otherCategory->name}}</a></li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="divfordropdown">
            <button class="dropbtn">Shop by Sub-Category</button>
            <!-- .asideProdList -->
            @include ('partials.aside', ['category' => $subcategory->category])
        </div>
       
        <div class="productsList">
            <div class="grid-containerProd row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-5 justify-content-center">
                @foreach($productVariations as $productVariation)
                    
                @php $product = $productVariation->product; @endphp
                <div class="grid-itemProd col mb-4">
                    <div class="card h-100">
                        <!-- Product image-->
                        <div class="imgcont1 card-footer">
                            <div class="imgcont2 text-center">
                                <a class="detailslink" href="{{url('/product/'.$productVariation->id)}}">
                                    @if(count($productVariation->product_images) > 0)
                                        @php $url = $productVariation->product_images->first()->url; @endphp
                                        <img class="imgdetails card-img-top" src="{{ asset("images/$url") }}" alt="..." />
                                    @else
                                        <img class="imgdetails card-img-top src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
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
                                <h6 class="prodprice">{{$productVariation->price}}€</h6>
                                
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<script>
    const container = document.getElementById('subcatname');
    //document.write(container.innerText);
    const result = container.innerText;
    //document.write(result);

    if (result == "All") {
        var divs = document.querySelectorAll('.asideProdList > [class^=px-3]');
        divs[0].style.backgroundColor="#ddd";
        divs[0].style.borderRadius = "5px";
    }

    var divs = document.querySelectorAll('.asideProdList > [class^=px-3]');
    for (var index = 1; index < divs.length; index++) {
        //document.write(divs[index].textContent);

        if (divs[index].textContent.includes(result)) {

            if ((divs[index].textContent[result.length + 1]) == '('){
                divs[index].style.backgroundColor="#ddd";
                divs[index].style.borderRadius = "5px";
            }
                
        }

    }

</script>


@endsection