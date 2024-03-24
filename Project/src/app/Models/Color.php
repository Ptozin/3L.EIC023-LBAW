<?php

namespace App\Models;

use Eloquent as Model;

class Color extends Model
{
    public $table = 'color';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'color',
    ];

    public function productVariations(){
        return $this->hasMany('App\Models\ProductVariation', 'id_color');
    }
}