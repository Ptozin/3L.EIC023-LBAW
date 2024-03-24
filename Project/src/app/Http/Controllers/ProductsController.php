<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function searchProducts()
    {
        try {
            $keyword = Request::get('keyword');
            $products = Product::whereRaw("name @@ plainto_tsquery('" . $keyword . "')")->paginate(8);

        } catch (\Exception $e) {
            return response(json_encode($e->getMessage()), 400);
        }

        return view('pages.search', ['keyword' => $keyword, 'products' => $products]);
    }
}
