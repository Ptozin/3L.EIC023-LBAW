<?php

namespace App\Models;

use Eloquent as Model;

class ProductVariation extends Model
{
    public $table = 'product_variation';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_prod', 
        'stock',
        'price',
        'id_size',
        'id_color'
    ];

    /**
    * The product this product variation belongs to
    */
    public function product() {
        return $this->belongsTo('App\Models\Product', 'id_prod');
    }

    /**
    * The size of this product variation
    */
    public function size() {
        return $this->belongsTo('App\Models\Size', 'id_size');
    }

     /**
    * The size of this product variation
    */
    public function color() {
        return $this->belongsTo('App\Models\Color', 'id_color');
    }

    public function product_images(){
        return $this->hasMany('App\Models\ProductImage', 'product_variation_id');
    }

    /**
     * Representative of table shopping cart
     */
    public function shopping_cart(){
        return $this->belongsToMany('App\Models\User', 'shopping_cart')
            ->withPivot('quantity');
    }

    /**
     * Representative of table wishlist
     */
    public function wishlist(){
        return $this->belongsToMany('App\Models\User', 'wishlist');
    }

    /**
     * Representative of table product purchase
     */
    public function product_purchase(){
        return $this->belongsToMany('App\Models\Purchase', 'product_purchase')
            ->withPivot('quantity');
    }
}