<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Eloquent as Model;
use HasCompositePrimaryKeyTrait;

class Review extends Model
{
    public $table = 'review';

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $primaryKey = ['user_id', 'id_product'];

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'comment',
        'rating',
        'date',
    ];


    /**
    * Function to get the id of the product
    */
    public function getIdProductAttribute()
    {
        return $this->attributes['id_product'];
    }

    /**
    * Function to get the id of the user
    */
    public function getUserIdAttribute()
    {
        return $this->attributes['user_id'];
    }

    /**
    * Function to set the product id of a review
    */
    public function setIdProductAttribute($value)
    {
        $this->attributes['id_product'] = $value;
    }


    /**
    * Function to set the user id of a review
    */
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = $value;
    }

    /**
    * Function to set the comment of a review
    */
    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = $value;
    }

    /**
    * Function to set the rating of a review
    */
    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = $value;
    }

    /**
    * Function to set the date of a review
    */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value;
    }

    /**
    * The user this review belongs to
    */
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
    * The product this review belongs to
    */
    public function product() {
        return $this->belongsTo('App\Models\Product', 'id_product');
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('user_id', $this->getAttribute('user_id'))
            ->where('id_product', $this->getAttribute('id_product'));
    }
}