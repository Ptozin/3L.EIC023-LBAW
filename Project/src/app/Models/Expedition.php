<?php

namespace App\Models;

use Eloquent as Model;

class Expedition extends Model
{
    public $table = 'expedition';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'delivery_date',
        'delivery_address',
        'shipping_cost',
        'exp_status'
    ];

    /**
    * The purchase this expedition belongs to
    */
    public function purchase() {
        return $this->belongsTo('App\Models\Purchase');
    }
}