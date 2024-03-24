<?php

namespace App\Models;

use Eloquent as Model;

class Notification extends Model
{
    public $table = 'notifications';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'notification_type'
    ];

    
    /**
    * The user this notification belongs to
    */
    public function user_id() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
    * The product_variation this notification belongs to
    */
    public function product_variations() {
        return $this->belongsTo('App\Models\ProductVariation', 'product_variation_id');
    }
}