<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plate', 'nickname', 'color', 'active', 'user_id', 'vmodel_id'
    ];

    /**
     * A vehicle belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * A vehicle can only have one model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmodel(){
        return $this->belongsTo('App\Vmodel');
    }

    public function brand(){
        return $this->vmodel->belongsTo('App\Brand', 'brands', 'id');
    }


    public function type(){
        return $this->vmodel->brand();
    }
//    /**
//     * Get all of the brands for the vehicle.
//     */
//    public function brands(){
//        return $this->vmodel->brand;
//    }
//    /**
//     * Get all of the brands for the vehicle.
//     */
//    public function types()
//    {
//        return $this->vmodel->type;
//    }
}
