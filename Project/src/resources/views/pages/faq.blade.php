@extends('layouts.app')

@section('title', 'SportsVerse - FAQ')

@section('content')

<a class="backlink" href="/homepage">
    <i id="leftarrow" class='fa fa-arrow-left'></i>
</a>

<header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
          <div class="text-center text-white">
              <h1 class="display-4 fw-bolder">FAQ</h1>
              <p class="lead fw-normal text-white-50 mb-0">All the informations about our page</p>
          </div>
      </div>
</header>

<div class="information">
    <img class="about_img" src="https://pbs.twimg.com/profile_images/1275525344834007041/E9XvfsD1_400x400.jpg" alt="no access to the image" />
    <div class="description">
        <h2>Payment methods available</h2>
        <p class="pay">We offer a variety of options to finalize your purchase:</p>
        <img class="payment" src="{{ asset('images/mbway.png') }}" alt="image is not in the directory">
        <img class="payment" src="{{ asset('images/multibanco.png') }}" alt="image is not in the directory">
        <img class="payment" id="float_image" src="{{ asset('images/mastercard.png') }}" alt="image is not in the directory">
        <img class="payment" src="{{ asset('images/visa.png') }}" alt="image is not in the directory">
        <p class="mb_note">MB: when choosing this option, the necessary data will be sent to your e-mail to proceed with the payment of your order in an ATM machine or by MB Net Payment (select the option Payment of Purchases > Services). You will have 24 hours to process the payment, otherwise your order will not be confirmed.</p>
    </div>
</div>

@endsection