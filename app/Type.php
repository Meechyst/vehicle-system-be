<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
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
     * There can be many models with same type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmodels(){
        return $this->hasMany('App\Vmodel');
    }

    /**
     * Get all of the vehicles for the type.
     */
    public function vehicles()
    {
        return $this->hasManyThrough('App\Vehicle', 'App\Vmodel');
    }
}
