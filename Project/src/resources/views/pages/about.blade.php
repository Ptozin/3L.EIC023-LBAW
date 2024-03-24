@extends('layouts.app')

@section('title', 'SportsVerse - About')

@section('content')

<a class="backlink" href="/homepage">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">About us</h1>
              <p class="lead fw-normal text-white-50 mb-0">Talking about our values</p>
          </div>
      </div>
</header>

<div class="information">
    <img class="about_img" src="https://pbs.twimg.com/profile_images/1275525344834007041/E9XvfsD1_400x400.jpg" alt="no access to the image" />
    <div class="description">
        <h3 class=" font-bold text-md lg:text-xl pb-4">Who is SportsVerse?</h3>
        <p class="aboutdesc">
            <span>SportsVerse! The only place you need for a healthy and active lifestyle.</span>
            <span>Every day we make new products. That is only possible because we developed our own proprietary technology that ensures that we bring you high quality, beautiful and fair priced products.</span>
            <span>This all starts in 2022 with 4 crazy individuals in the LBAW class that thinked they can (or try to) change the world for the better. Right now, we are only 4, but we aim going to thousands of crazy but focused people. Will is a skill.</span>
        </p>
    </div>
</div>


@endsection