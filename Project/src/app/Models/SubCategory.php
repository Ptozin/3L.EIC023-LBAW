<?php

namespace App\Models;

use Eloquent as Model;

class SubCategory extends Model
{
    public $table = 'sub_category';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'name', 'id_category'
    ];

    /**
    * The category this subcategory belongs to
    */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'id_category');
    }

    public function products(){
        return $this->hasMany('App\Models\Product', 'id_sub_category');
    }
}