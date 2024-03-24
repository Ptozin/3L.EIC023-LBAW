@extends('layouts.app')

@section('title', 'SportsVerse - Contacts')

@section('content')

<a class="backlink" href="/homepage">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">Contacts</h1>
              <p class="lead fw-normal text-white-50 mb-0">All the ways you can get to us</p>
          </div>
      </div>
</header>

<div class="contact_information">
    <img class="about_img" src="https://pbs.twimg.com/profile_images/1275525344834007041/E9XvfsD1_400x400.jpg" alt="no access to the image" />
    <div class="contact_description">
        <h2 class="contact">Contact us!</h2>
        <h5>phone number</h5>
        <p>+351 987654321</p>
        <p>Monday to Friday:<br/> 09:00 to 18:00 (Porto)</p>
        <h5 class="email">email</h5>
        <p>sportsverse2022@sports.verse.com</p>
    </div>
</div>

@endsection