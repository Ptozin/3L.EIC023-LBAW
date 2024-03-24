<?php

namespace App\Models;

use Eloquent as Model;

class Purchase extends Model
{
    public $table = 'purchase';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'payment_method',
        'date',
        'price',
        'pur_status'
    ];

    /**
    * The user this purchase belongs to
    */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * The expeditions this purchase owns.
     */
    public function expedition(){
        return $this->hasOne('App\Model\Expedition');
    }

    /**
     * Representative of table product purchase
     */
    public function product_purchase(){
        return $this->belongsToMany('App\Models\ProductVariation', 'product_purchase')
            ->withPivot('quantity');
    }
}