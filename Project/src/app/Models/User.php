<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    public $table = 'users';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 
        'name', 
        'password', 
        'birthdate', 
        'address', 
        'phone_number', 
        'is_admin', 
        'blocked'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The cards this user owns.
     */
     public function cards() {
      return $this->hasMany('App\Models\Card');
    }

    /**
     * The reviews this user owns.
     */
    public function reviews(){
        return $this->hasMany('App\Models\Review', 'user_id');
        //return $this->belongsToMany('App\Models\Review', 'review');
    }

    /**
     * The purchases this user owns.
     */
    public function purchases(){
        return $this->hasMany('App\Models\Purchase', 'user_id');
    }

    /**
     * Representative of table shopping cart
     */
    public function shopping_cart(){
        return $this->belongsToMany('App\Models\ProductVariation', 'shopping_cart')
            ->withPivot('quantity');
    }

    /**
     * Representative of table wishlist
     */
    public function wishlist(){
        return $this->belongsToMany('App\Models\ProductVariation', 'wishlist');
    }

    /**
     * The notifications this user owns.
     */
    public function notifications(){
        return $this->hasMany('App\Models\Notification', 'user_id');
    }

    public function delete_user(){
        
        return;
    }
}
