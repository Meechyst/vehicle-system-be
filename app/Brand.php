<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];


    /**
     * A brand can have many models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmodels(){
        return $this->hasMany('App\Vmodel');
    }

    /**
     * Get all of the vehicles for the brand.
     */
    public function vehicles()
    {
        return $this->hasManyThrough('App\Vehicle', 'App\Vmodel');
    }


}
