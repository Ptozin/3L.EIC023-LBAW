<?php
 
namespace App\Http\Controllers;
 
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\SubCategory;
use App\Models\Size;
use App\Models\Color;
use App\Models\Notification;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Show the product page for a given product.
     * Shows default product variation
     * 
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id, Request $request)
    {   

        if(isset($request->product_variation)) {
            $variation = ProductVariation::find($request->product_variation);
            return view('pages.product')->with('variation', $variation);
        }
        else {
            #dd("didn't enter");   
            $variation = ProductVariation::find($id);
            if(count($variation->product->product_variations) > 0)
            {   
                return view('pages.product')->with('variation', $variation);
            }
            return redirect()->back()->with('danger', 'Product Variation not set');
            }
    }

    public function showEditProduct($id)
    {
        try {
            $user = Auth::user();
            if($user->is_admin == TRUE){
                return view('pages.product_edit', ['variation' => ProductVariation::find($id)]);
            }
            else{
                return response(json_encode("You are not allowed to access this page"), 500);
            }

        } catch (\Exception $e) {
            return response(json_encode("Failed to enter edit product form"));
        }
    }

    /**
     * Update product or product variation.
     *
     * @param int  $id, 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function updateProduct($id, Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            if ($user->is_admin) {
                $variation = ProductVariation::findOrFail($id);
                $product = $variation->product;
                $this->authorize('edit', $user);
                $this->validate($request, [
                    'name' => 'required|string|max:255',
                    'short_description' => 'required|string|max:255',
                    'long_description' => 'required|string|max:1000',
                    'manufacturer' => 'required|string|max:255',
                    'stock' => 'required|numeric',
                    'price' => 'required|numeric',
                ]);
                if (isset($request->name)) {
                    $product->name = $request->name;
                }
                
                if (isset($request->short_description)) {
                    $product->short_description = $request->short_description;
                }

                if (isset($request->long_description)) {
                    $product->long_description = $request->long_description;
                }

                if (isset($request->manufacturer)) {
                    $product->manufacturer = $request->manufacturer;
                }
                if (isset($request->stock)) {
                    #dd($variation->wishlist()->get());
                    if($request->stock > 0 && $variation->stock  == 0) {
                        # Issue a stock notification
                        foreach($variation->wishlist()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Product available');
                        }

                        foreach($variation->shopping_cart()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Product available');
                        }
                        
                    } else if($request->stock == 0 && $variation->stock  > 0) {
                        # Issue a out of stock notification
                        foreach($variation->wishlist()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Product out of stock');
                        }
                        foreach($variation->shopping_cart()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Product out of stock');
                        }
                    }
                    $variation->stock = $request->stock;
                }

                if (isset($request->price)) {
                    if($request->price != $variation->price) {
                        foreach($variation->wishlist()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Price change');
                        }
                        foreach($variation->shopping_cart()->get() as $user) {
                            (new NotificationController)->notifyUser($user->id, $product->id, 'Price change');
                        }
                    }
                    $variation->price = $request->price;
                }


                $product->update();
                $variation->update();

                return redirect(route('product', ['id' => $id])); 
            } else {
                return response(json_encode("You are not authorized to perform this action"), 403);
            }
        } catch (\Exception $e) {
            dd($e);
            return response(json_encode("Failed to update product"), 500);
        }
    }

    public function showAddProduct()
    {
        try {
            $user = Auth::user();
            if($user->is_admin == TRUE){
                return view('pages.add_product', ['subcategories' => Subcategory::get()]);
            }
            else{
                return response(json_encode("You are not allowed to access this page"), 500);
            }

        } catch (\Exception $e) {
            return response(json_encode("Failed to enter add product form"));
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Product
     */
    protected function addProduct(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();

            if ($user->is_admin) {
                $this->authorize('edit', $user);
                echo($user);
                echo($request->id_sub_category);
                $this->validate($request, [
                    'name' => 'required|string|max:255',
                    'short_description' => 'required|string|max:255',
                    'long_description' => 'required|string|max:1000',
                    'manufacturer' => 'required|string|max:255',
                    'id_sub_category' => 'required|integer|min:1|exists:App\Models\SubCategory,id',
                    'stock' => 'required|numeric',
                    'price' => 'required|numeric',
                    'id_size' => 'required|integer|min:1|exists:App\Models\Size,id',
                    'id_color' => 'required|integer|min:1|exists:App\Models\Color,id',
                ]);

                $product = Product::create([ 
                    'name' => $request['name'],
                    'short_description' => $request['short_description'],
                    'long_description' => $request['long_description'],
                    'rating' => 0.0,
                    'manufacturer' => $request['manufacturer'],
                    'id_sub_category' => $request['id_sub_category'],
                ]);

                $variation = ProductVariation::create([ 
                    'id_prod' => $product['id'],
                    'stock' => $request['stock'],
                    'price' => $request['price'],
                    'id_size' => $request['id_size'],
                    'id_color' => $request['id_color'],
                ]);

                $files = $request->file('images');

                if($request->hasFile('images'))
                {
                    foreach ($files as $file) {
                        $timestamp = Carbon::now()->format('Y-m-d');
                        $filename = $timestamp . $file->getClientOriginalName();
                        $this->addProductImage(1, $filename);
                        $file->move(public_path() . '/images/', $filename);
                        DB::transaction(function () use ($filename, $variation) {
                            $image = new ProductImage;
                            $image->product_variation_id = $variation->id;
                            $image->url = $filename;
                            $image->save();
                        });                       
                    }
                }


                return redirect(route('product', ['id' => $variation->id])); 
            } else {
                return response(json_encode("You are not authorized to perform this action"), 403);
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to add product"), 500);
        }
    }

    public function showAddProductVariation($id)
    {
        try {
            $user = Auth::user();
            if($user->is_admin == TRUE){
                return view('pages.add_product_variation', ['variation' => ProductVariation::find($id)]);
            }
            else{
                return response(json_encode("You are not allowed to access this page"), 500);
            }

        } catch (\Exception $e) {
            return response(json_encode("Failed to enter add product form"));
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\ProductVariation
     */
    protected function addProductVariation($id, Request $request)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();

            if ($user->is_admin) {
                $this->authorize('edit', $user);
                $this->validate($request, [
                    'stock' => 'required|numeric',
                    'price' => 'required|numeric',
                    'id_size' => 'required|integer|min:1|exists:App\Models\Size,id',
                    'id_color' => 'required|integer|min:1|exists:App\Models\Color,id',
                ]);

                $variation = ProductVariation::create([ 
                    'id_prod' => $id,
                    'stock' => $request['stock'],
                    'price' => $request['price'],
                    'id_size' => $request['id_size'],
                    'id_color' => $request['id_color'],
                ]);

                return redirect(route('product', ['id' => $variation->id]));  
            } else {
                return response(json_encode("You are not authorized to perform this action"), 403);
            }
        } catch (\Exception $e) {
            return response(json_encode("Failed to add product variation"), 500);
        }
    }

     /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\ProductImage
     */
    protected function addProductImage($id_variation, $filename)
    {
        try {
            if (!Auth::check()) {
                return response(json_encode("You are not registered"));
            }
            $user = Auth::user();
            $this->authorize('editAdmin', $user);
            $image = ProductImage::create([
                'product_variation_id' => $id_variation,
                'url' => $filename,
            ]);
            dd($image);
            return true;

        } catch (\Exception $e) {
            return response(json_encode("Failed to add product variation"), 500);
        }

    }

}