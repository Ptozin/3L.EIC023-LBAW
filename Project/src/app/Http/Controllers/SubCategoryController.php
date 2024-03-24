<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
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
     * Lits a slider containing all subcategories of a given category
     * and lists every product of the subcategory chosen
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
    public function showSlider($id) // ISTO ESTÁ DUPLICADO COM CATEGORYCONTROLLER !!!!!!!!!!!
    {
        try {
            $category = Category::findOrFail($id);
            return view('pages.subcategoriesslider')->with('subcategories',$category->subcategories)->with('name', $category->name);
        } catch(\Exception $e) {
            return response(json_encode("Sub category not found"));
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
          $productVariations = [];
          $subcategory = SubCategory::findOrFail($id);
          $allCategories = Category::all();
          
          foreach($subcategory->products as $product){
            $productVariations[] = $product->product_variations->sortByDesc('price')->take(1)->first();
          }
      
          foreach($productVariations as $item){
            if(!(isset($item))){
              if (($key = array_search($item, $productVariations)) !== false) {
                unset($productVariations[$key]);
              }
            }
          }
          $sort = request('sort');
        if(isset($sort)){
            usort($productVariations, [$this, $sort]);
        }
          return view('pages.subcategory')->with('productVariations', $productVariations)->with('subcategory', $subcategory)->with('allCategories', $allCategories);
      } catch(\Exception $e) {
          return response(json_encode("Sub category not found"));
      }
    }

    public function showAddSubCategory()
    {
        try {
            $user = Auth::user();
            if($user->is_admin == TRUE){
                return view('pages.add_subcategory');
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
     * @return \App\Models\SubCategory
     */
    protected function addSubCategory(Request $request)
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
                    'id_category' => 'required|integer|min:1|exists:App\Models\Category,id',
                ]);
                
                $subcategory = SubCategory::create([ 
                    'name' => $request['name'],
                    'id_category' => $request['id_category'],
                ]);

                return redirect(route('subcategory', ['id' => $subcategory->id])); 
            } else {
                return response(json_encode("You are not authorized to perform this action"), 403);
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to add subcategory"), 500);
        }
    }
}
