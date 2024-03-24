<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductVariation;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShoppingCartController extends Controller
{

    /**
     * Show shopping cart for the authenticated user
     * @return \Illuminate\View\View
     */
    public function showShoppingCart(){

        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $products = $user->shopping_cart()->get();
            $total = $products->sum(function($t){ 
                return $t->price*$t->pivot->quantity; 
            });
            return view('pages.shopping_cart', ['total'=> $total, 'products' => $products]);
        } catch(\Exception $e) {
            return response(json_encode("Error showing shopping cart"), 500);
        }
    }

    /**
     * Add to the user's shopping cart a given product
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function addShoppingCartProduct(Request $request)
    {

        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $product = ProductVariation::findOrFail($request->id);
            $quantity = $request->quantity;
            if ($user->shopping_cart()->where('product_variation_id', $request->id)->count() > 0) {
                return response(json_encode("You already have this product in your cart"), 500);
            }

        } catch (\Exception $e) {
            return response(json_encode("Error adding product to shopping cart"), 400);
        }

        if($user->is_admin == FALSE){
            if ($product != null) {
                $user->shopping_cart()->attach($product, array('quantity' => $quantity));
                return response(json_encode("Product added to Cart"), 200);
            } else {
                return response(json_encode("That product does not exist or is not available"), 404);
            }
        }
        else{
            return response(json_encode("An administrator account can not add products to cart"), 500);
        }

    }

    /**
     * Remove from the user's shopping cart a given product
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function removeShoppingCartProduct(Request $request)
    {
        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $product = $user->shopping_cart()->where('product_variation_id', $request->id)->first();

        } catch (\Exception $e) {
            return response(json_encode("Error removing product from cart"), 500);
        }

        if ($product != null) {
            $user->shopping_cart()->detach([$request->id]);
            $products = $user->shopping_cart()->get();
            $total = $products->sum(function ($t) {
                return $t->price * $t->pivot->quantity;
            });
            return response(json_encode(array("Message" => "Product deleted from cart", "Price" => $total)), 200);
        } else {
            return response(json_encode(array("Message" => "The product is not in your cart", "Price" => null)), 404);
        }
    }

    /**
     * Update the user's shopping cart for a given product
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function updateShoppingCartProduct(Request $request)
    {

        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $product = $user->shopping_cart()->where('product_variation_id', $request->id)->first();

        } catch (\Exception $e) {
            return response(json_encode("Error updating product from cart"), 400);
        }

        if ($product != null) {

            try {
                $product->pivot->quantity = intval($request->quantity);
                $product->pivot->update();
                $products = $user->shopping_cart()->get();
                $total = $products->sum(function ($t) {
                    return $t->price * $t->pivot->quantity;
                });
                return response(json_encode(array("Message" => "Your product quantity was updated", "Price" => $total)), 200);
            } catch (\Exception $e) {
                return response(json_encode(array('Message' => "There are not enough available products", "Price" => null)), 500);
            }

        }
    }

    /**
     * Register checkout form in the database
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function checkout(Request $request)
    {

        try{
            if (!Auth::check()) {
                return response(json_encode("You are not able to checkout"), 401);
            }

            $user = Auth::user();
            if($user->is_admin == FALSE){
                $payment_method = $request->payment_method;
                $products = $user->shopping_cart()->get();
                $total = $products->sum(function ($t) {
                    return $t->price * $t->pivot->quantity;
                });
                $total = number_format($total, 2, '.', '');

                $this->validate($request, [
                    'cardname' => 'required|string|max:255',
                    'cardnumber' => 'required|string|regex:/^\d{16}$/',
                    'cvv' => 'required|string|regex:/^\d{3}$/',
                ], [
                    'cardnumber' => 'The input field must contain exactly 16 digits.',
                    'cvv' => 'The input field must contain exactly 3 digits.',
                ]);

                DB::transaction(function () use ($total, $payment_method, $products) {

                    $purchase = new Purchase;
                    $purchase->payment_method = $payment_method;
                    $purchase->date = Carbon::now();
                    $purchase->user_id = Auth::id();
                    $purchase->price = $total;
                    $purchase->pur_status = 'Payment Pending';
                    $purchase->save();
                    $purchase_products = [];
                    foreach ($products as $product) {
                        $purchase_products[$product->id] = ['quantity' => $product->pivot->quantity];
                    }

                    $purchase->product_purchase()->sync($purchase_products);

                });

                foreach ($products as $product) {
                    $user->shopping_cart()->detach();
                }

                return redirect()->route('profile');
            }
            else{
                return response(json_encode("An administrator account can not checkout"), 401);
            }
        } catch (\Exception $e) {
            return response(json_encode("Something went wrong checking out"), 403);
        }

        
    }

    public function __construct()
    {
        $this->middleware('auth');
    }
}