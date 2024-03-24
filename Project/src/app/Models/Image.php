<?php

namespace App\Models;

use Eloquent as Model;

class Image extends Model
{
    public $table = 'product_image';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'url',
    ];

    /**
    * The Product Variation this image belongs to
    */
    public function product_variation() {
        return $this->belongsTo('App\Models\ProductVariation');
    }
}