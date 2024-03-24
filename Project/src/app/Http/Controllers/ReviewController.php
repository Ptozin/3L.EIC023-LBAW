<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Review;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReviewController extends Controller
{

    /**
     * Show review form for a given product
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id) 
    {
        try {
            $user = Auth::user();
            $this->authorize('edit', $user);
            $variation = ProductVariation::findOrFail($id);
            foreach($user->purchases as $purchase) { // TODO change this whenever possible
                foreach($purchase->product_purchase as $prod_variation) {
                    if($prod_variation->id == $id){
                        if(count($user->reviews) > 0) {
                            foreach($user->reviews as $review) {
                                if($review->id_product == $variation->product->id) {
                                    return view('pages.review', ['product'=> $variation->product, 'product_variation' => $variation, 'user'=>$user, 'purchase' => $purchase, 'review' => $review]);
                                }
                            }
                        }
                        return view('pages.review', ['product'=> $variation->product, 'product_variation' => $variation, 'user'=>$user, 'purchase' => $purchase, 'review' => []]);
                    }
                }
            }
            return response(json_encode("You can't review a product you haven't bought"));

        } catch(\Exception $e) {
            return response(json_encode("Error showing product review"));
        }
    }

    /**
     * Add review for a given product
     * @param int $id
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function addReview($id, Request $request)
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($id);
            $this->authorize('edit', $user);

            $this->validate($request, [ // it auto returns in case of failure
                'comment' => 'required|string|max:255|',
            ]);
            $score = (int) $request->score;
            if (!($score >= 1 && $score <= 5)) {
                return response(json_encode(array("Message" => "Rating is not valid")), 500);
            }
            DB::transaction(function () use ($score, $request, $product, $user) {
                $review = new Review;
                $review->user_id = Auth::id();
                $review->id_product = $product->id;
                $review->comment = $request->comment;
                $review->rating = $score;
                $review->date = Carbon::now()->format('Y-m-d');
                $review->save();
            });

            return redirect()->to(route('homepage'));

        } catch (\Exception $e) {
            return response(json_encode(array("Message" => "Error adding review")), 500);
        }
    }

    public function editReview($id, Request $request) 
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($id);
            $review = Review::where(['user_id' => Auth::id(), 'id_product' => $product->id])->firstOrFail();
            $this->authorize('edit', $user);
            if($request->input('action') == 'Delete Review') {
                $review->delete();
            }
            else {
                $this->validate($request, [
                    'comment' => 'required|string|max:255|',
                ]);
                $score = (int) $request->score;
                if (!($score >= 1 && $score <= 5)) {
                    return response(json_encode(array("Message" => "Rating is not valid")), 500);
                }
                DB::transaction(function () use ($review, $score, $request) {

                    $review->comment = $request->comment;
                    $review->rating = $score;
                    $review->date = Carbon::now()->format('Y-m-d');

                    $review->update();
                });
            }
        } catch (\Exception $e) {
            return response(json_encode(array("Message" => "Error editing review")), 500);
        }
        return redirect()->to(route('homepage'));
    }

    public function deleteReview($user_id, $id_product, Request $request) 
    {
        try {
            #dd($user_id);
            $user = Auth::user();
            $this->authorize('editAdmin', $user);
            $product = Product::findOrFail($id_product);
            $reviewOwner = User::findOrFail($user_id);
            $review = Review::where(['user_id' => $reviewOwner->id, 'id_product' => $product->id])->firstOrFail();
            $review->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return response(json_encode(array("Message" => "Error deleting review")), 500);
        }
    }
}
