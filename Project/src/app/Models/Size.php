<?php

namespace App\Models;

use Eloquent as Model;

class Size extends Model
{
    public $table = 'size';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'size',
    ];

    public function productVariations(){
        return $this->hasMany('App\Models\ProductVariation', 'id_size');
    }
}