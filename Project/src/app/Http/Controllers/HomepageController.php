<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;

class HomepageController extends Controller {
    /**
     * Show the home page of the website
     * @return View
     */
    public function show() {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                if($user->blocked) {
                    return redirect(route('logout'));
                }
            }
            $limit = 6;
            $ourBrand = 'sportsverse';
            $bestRatingProducts = Product::orderBy('rating', 'desc')->take($limit)->get();
            $ownProducts = Product::where('manufacturer', $ourBrand)->orderBy('rating', 'desc')->take($limit)->get();
            $ownProductsVar = [];
            $categories = Category::all();
            $bestRatingProductsVar = [];

            foreach($bestRatingProducts as $product){
                $bestRatingProductsVar[] = $product->product_variations->where('stock', '>', 0)->sortByDesc('price')->take(1)->first();
            }

            foreach($ownProducts as $product){
                $ownProductsVar[] = $product->product_variations->where('stock', '>', 0)->sortByDesc('price')->take(1)->first();
            }
       
            foreach($bestRatingProductsVar as $item){
            if(!(isset($item))){
                if (($key = array_search($item, $bestRatingProductsVar)) !== false) {
                unset($bestRatingProductsVar[$key]);
                }
            }
            }
   
            foreach($ownProductsVar as $item){
                if(!(isset($item))){
                    if (($key = array_search($item, $ownProductsVar)) !== false) {
                    unset($ownProductsVar[$key]);
                    }
                }
            }
            return view('pages.homepage')->with('bestRatingProductVariations', $bestRatingProductsVar)->with('categories', $categories)->with('ownProductVariations', $ownProductsVar);
        } catch(\Exception $e) {
            return response(json_encode("Error showing Home Page"), 500);
        }
    }
}