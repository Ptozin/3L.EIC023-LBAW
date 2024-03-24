@extends('layouts.app')

@section('title', 'SportsVerse - Categories')

@section('content')



<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">Shop by Category</h1>
              <p class="lead fw-normal text-white-50 mb-0">And quickly get what you need</p>
          </div>
      </div>
</header>

<a class="backlink" href="{{ url('/homepage') }}">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<section class="categorygrid">
    <div id="gridcategoriesSpace" class="gridcategoriesSpace">
        <div id="griddivlinkcat" class="bg-background-off p-6 my-6 rounded homepageCategories">
            @each('partials.category', $categories, 'category')
        </div>
    </div>
</section>


@endsection