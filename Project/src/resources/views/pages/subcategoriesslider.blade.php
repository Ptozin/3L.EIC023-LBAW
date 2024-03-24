@extends('layouts.app')

@section('title', 'SportsVerse - SubCategories')

@section('content')


<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">Shop by Sub-Category</h1>
              <p class="lead fw-normal text-white-50 mb-0">With this shop homepage template</p>
          </div>
      </div>
</header>


<a href="{{url('/category')}}"><h3 class=" font-bold text-md lg:text-xl pb-4">&#x2190;</h3></a>
<div class="bg-background-off p-6 my-6 rounded">
    <h3 class=" font-bold text-md lg:text-xl pb-4">Subcategories of {{$name}}</h3>
    @each('partials.subcategoryslider', $subcategories, 'subcategory')
</div>




<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">Subcategories of {{$name}}</h1>
              <p class="lead fw-normal text-white-50 mb-0">choose one to dive into our offers</p>
          </div>
      </div>
</header>

<!-- Section-->
<section class="py-5">
    <!-- seta para tras -->
      <a href="{{url('/category')}}">
      <i id="leftarrow" class='fa fa-arrow-left'></i>


      @each('partials.subcategory', $subcategories, 'subcategory')

      <div class="container px-4 px-lg-5 mt-5">
          <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
              <div class="col mb-5">
                  <div class="card h-100">
                      <!-- Product image-->
                      <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
                      <!-- Product details-->
                      <div class="card-body p-4">
                          <div class="text-center">
                              <!-- Product name-->
                              <h5 class="fw-bolder">Fancy Product</h5>
                              <!-- Product price-->
                              $40.00 - $80.00
                          </div>
                      </div>
                      <!-- Product actions-->
                      <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                          <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                      </div>
                  </div>
              </div>
              <div class="col mb-5">
                  <div class="card h-100">
                      <!-- Sale badge-->
                      <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                      <!-- Product image-->
                      <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
                      <!-- Product details-->
                      <div class="card-body p-4">
                          <div class="text-center">
                              <!-- Product name-->
                              <h5 class="fw-bolder">Special Item</h5>
                              <!-- Product reviews-->
                              <div class="d-flex justify-content-center small text-warning mb-2">
                                  <div class="bi-star-fill"></div>
                                  <div class="bi-star-fill"></div>
                                  <div class="bi-star-fill"></div>
                                  <div class="bi-star-fill"></div>
                                  <div class="bi-star-fill"></div>
                              </div>
                              <!-- Product price-->
                              <span class="text-muted text-decoration-line-through">$20.00</span>
                              $18.00
                          </div>
                      </div>
                      <!-- Product actions-->
                      <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                          <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                      </div>
                  </div>
              </div>
              <div class="col mb-5">
                  <div class="card h-100">
                      <!-- Sale badge-->
                      <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>
                      <!-- Product image-->
                      <img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
                      <!-- Product details-->
                      <div class="card-body p-4">
                          <div class="text-center">
                              <!-- Product name-->
                              <h5 class="fw-bolder">Sale Item</h5>
                              <!-- Product price-->
                              <span class="text-muted text-decoration-line-through">$50.00</span>
                              $25.00
                          </div>
                      </div>
                      <!-- Product actions-->
                      <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                          <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">Add to cart</a></div>
                      </div>
                  </div>
              </div>
              
          </div>
      </div>
  </section>

@endsection