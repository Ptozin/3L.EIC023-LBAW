<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class ProductPolicy {
    use HandlesAuthorization;

    public function review(User $user, Product $product) {
        if($product->reviews->where('id_user', $user->id)->count() > 0)
          return false;

      $purchases = $user->purchases;
      foreach($purchases as $purchase) {
        if(($purchase->product_purchase->whereIn('product_variation_id', $product->product_variations->id))->count() > 0)
          return true;
      }
      return false;
    }
}
?>