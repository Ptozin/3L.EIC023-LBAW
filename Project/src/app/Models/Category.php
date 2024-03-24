<?php

namespace App\Models;

use Eloquent as Model;

class Category extends Model
{
    //use Notifiable;
    public $table = 'category';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function subcategories(){
        return $this->hasMany('App\Models\SubCategory', 'id_category');
    }
}