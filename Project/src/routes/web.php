<?php

use Illuminate\Http\Request;
    

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Home

Route::get('/', 'Auth\LoginController@home');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

//View Profile
Route::get('/profile', 'UserController@show')->name('profile');
Route::put('/profile', 'UserController@updatePurchaseStatus');
Route::get('/profile/users', 'UserController@show');
Route::get('/user/{id}', 'UserController@showAdmin');
Route::put('/user/{id}', 'UserController@updatePurchaseStatus');

//Edit Profile
Route::get('/profile/edit', 'UserController@showEdit');
Route::get('/user/{id}/edit', 'UserController@showEditAdmin');

//Add Products
Route::get('/category/add', 'CategoryController@showAddCategory');
Route::post('api/category', 'CategoryController@addCategory');
Route::get('/subcategory/add', 'SubCategoryController@showAddSubCategory');
Route::post('api/subcategory', 'SubCategoryController@addSubCategory');
Route::get('/product/add', 'ProductController@showAddProduct');
Route::post('api/product', 'ProductController@addProduct');

// Listing Categories and Subcategories
Route::get('/category', 'CategoryController@listSlider');
Route::get('/category/{id}', 'CategoryController@showSlider');

//HomePage and Product Listing Pages
Route::get('/homepage', 'HomepageController@show')->name('homepage');
Route::get('/products/category/{id}', 'CategoryController@show')->name('category');
Route::get('/products/subcategory/{id}', 'SubCategoryController@show')->name('subcategory');

// Product Page
Route::get('/product/{id}', 'ProductController@show')->name('product');
Route::get('/product/{id}/edit', 'ProductController@showEditProduct');
Route::get('/product/{id}/addvariation', 'ProductController@showAddProductVariation');
Route::post('api/product/{id}', 'ProductController@updateProduct')->name('edit_product');
Route::post('api/productvariation/{id}', 'ProductController@addProductVariation');


// Static Pages
Route::view('about', 'pages.about');
Route::view('services', 'pages.services');
Route::view('contacts', 'pages.contacts');
Route::view('faq', 'pages.faq');


// User
Route::post('api/user/{id}', 'UserController@updateProfile')->name('update');
Route::post('user/delete/{id}', 'UserController@deleteUser')->name('deleteuser');
Route::post('user/block/{id}', 'UserController@blockUser')->name('blockuser');

// Search
Route::get('search','ProductsController@searchProducts')->name('search_products');

// ShoppingCart
Route::get('shopping_cart', 'ShoppingCartController@showShoppingCart')->name('shopping_cart');
Route::delete('shopping_cart', 'ShoppingCartController@removeShoppingCartProduct');
Route::post('shopping_cart', 'ShoppingCartController@addShoppingCartProduct');
Route::put('shopping_cart', 'ShoppingCartController@updateShoppingCartProduct');
Route::post('checkout', 'ShoppingCartController@checkout')->name('checkout');

// Wishlist
Route::get('wishlist','WishlistController@showWishlist')->name('wishlist');
Route::post('wishlist', 'WishlistController@addWishlistProduct');
Route::delete('wishlist', 'WishlistController@removeWishlistProduct');

// Review
Route::get('review/{id}','ReviewController@show')->name('review');
Route::post('review/{id}','ReviewController@addReview')->name('add_review');
Route::post('review/edit/{id}','ReviewController@editReview')->name('edit_review');
Route::post('review/delete/{user_id}/{id_product}','ReviewController@deleteReview')->name('delete_review');

Route::post('notification/{id}','NotificationController@deleteNotification')->name('notification');
