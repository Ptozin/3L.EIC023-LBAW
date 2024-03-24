<?php

namespace App\Models;

use Eloquent as Model;

class Product extends Model
{
    public $table = 'product';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'name',
        'short_description',
        'long_description',
        'rating',
        'manufacturer',
        'id_sub_category'
    ];

    /**
    * The subcategory this product belongs to
    */
    public function sub_category() {
        return $this->belongsTo('App\Models\SubCategory', 'id_sub_category');
    }

    /**
     * The variations this product owns.
     */
    public function product_variations(){
        return $this->hasMany('App\Models\ProductVariation', 'id_prod');
    }

    /**
     * The reviews this product owns.
     */
    public function reviews(){
        return $this->hasMany('App\Models\Review', 'id_product');
    }
}