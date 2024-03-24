<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{

    private function nameasc($a, $b) {
        return strcmp($a->product->name, $b->product->name);
    }

    private function namedesc($a, $b) {
        return strcmp($b->product->name, $a->product->name);
    }

    private function priceasc($a, $b) {
        return $a->price > $b->price;
    }

    private function pricedesc($a, $b) {
        return $a->price < $b->price;
    }

    private function rating($a, $b) {
        return $a->product->rating < $b->product->rating;
    }

    /**
     * Lits a slider containing all categories available on the website
     * @return \Illuminate\View\View
     */
    public function listSlider()
    {
        return view('pages.categoriesslider')->with('categories', Category::all())->with('slider', true);
    }

    /**
     * Lits a slider containing all sub categories of a given category on the website
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showSlider($id)
    {
        try {
            $category = Category::findOrFail($id);

            if(count($category->subcategories) > 0) {
                return view('pages.subcategoriesslider')->with('subcategories', $category->subcategories)->with('name', $category->name);
            }
            else {
                return response(json_encode("The category does not have any sub categories"));
            }
        } catch(\Exception $e) {
            return response(json_encode("Category not found"), 404);
        }
    }

    /**
     * Lits a slider containing all products of a given subcategory
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
      try {
        $products = [];
        $productVariations = [];
        $productsaux = [];
        $category = Category::findOrFail($id);
        $allCategories = Category::all();

        foreach ($category->subcategories as $subcategory) {
            $productsaux[] = $subcategory->products;
        }

        foreach ($productsaux as $prod_array) {
            foreach ($prod_array as $product) {
              $products[] = $product;
            }
        }
        foreach ($products as $product) {
            $productVariations[] = $product->product_variations->sortByDesc('price')->take(1)->first();
        }

        foreach ($productVariations as $item) {
            if (!(isset($item))) {
                if (($key = array_search($item, $productVariations)) !== false) {
                  unset($productVariations[$key]);
                }
            }
        }
        $sort = request('sort');
        if(isset($sort)){
            usort($productVariations, [$this, $sort]);
        }
        return view('pages.category')->with('productVariations', $productVariations)->with('category', $category)->with('allCategories', $allCategories);
      } catch(\Exception $e) {
        return response(json_encode("Category not found"), 404);
      }
    }

    public function showAddCategory()
    {
        try {
            $user = Auth::user();
            if($user->is_admin == TRUE){
                return view('pages.add_category');
            }
            else{
                return response(json_encode("You are not allowed to access this page"), 500);
            }

        } catch (\Exception $e) {
            return response(json_encode("Failed to enter add category form"));
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Category
     */
    protected function addCategory(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();

            if ($user->is_admin) {
                $this->authorize('edit', $user);
                $this->validate($request, [
                    'name' => 'required|string|max:255',
                ]);

                $category = Category::create([ 
                    'name' => $request['name'],
                ]);

                return redirect(route('category', ['id' => $category->id])); 
            } else {
                return response(json_encode("You are not authorized to perform this action"), 403);
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to add category"), 500);
        }
    }
}
