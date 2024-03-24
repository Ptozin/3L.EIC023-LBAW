<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductVariation;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{

    public function showWishlist()
    {

        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $variations = $user->wishlist()->get();      
            
            return view('pages.wishlist', ['variations' => $variations]);
        } catch (\Exception $e) {
            return response(json_encode("Error showing wishlist"), 500);
        }

    }

    public function addWishlistProduct(Request $request)
    {

        try {
            $product_variation = ProductVariation::findOrFail($request->id);
        } catch (\Exception $e) {
            return response(json_encode("Error getting product"), 500);
        }

        if ($product_variation != null) {
            $user = Auth::user();
            $this->authorize('edit', $user);
            if($user->wishlist()->where('product_variation_id', $request->id)->count() > 0){
                return response(json_encode("You already have this product in your wishlist"), 401);
            }
            $user->wishlist()->attach($product_variation);

            return response(json_encode("Product added to Wishlist"), 200);
        } else {
            return response(json_encode("That product does not exist or is not available"), 404);
        }

    }


    public function removeWishlistProduct(Request $request) {
        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $product_variation = $user->wishlist()->where('product_variation_id', $request->id)->first();

        }catch(\Exception $e) {
            return response(json_encode("Error removing product from wishlist"), 500);
        }

        if($product_variation != null){
            $user->wishlist()->detach([$request->id]);
            return response(json_encode("Product deleted from wishlist"), 200);
        }
        else{
            return response(json_encode("That produt is not on the user's wishlist"), 404);
        }
    }

    public function __construct()
    {
        $this->middleware('auth');
    }
}