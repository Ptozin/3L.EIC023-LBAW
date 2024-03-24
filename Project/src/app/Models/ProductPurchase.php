<?php

namespace App\Models;

use Eloquent as Model;

class ProductPurchase extends Model
{
    public $table = 'product_purchase';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'quantity',
    ];

    /**
     * Representative of table purchase
     */
    public function purchase(){
        return $this->belongsToMany('App\Models\Purchase');
    }

    /**
     * Representative of table product variation
     */
    public function product_variation(){
        return $this->belongsTo('App\Models\ProductVariation');
    }
}