<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vmodel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'year', 'brand_id', 'type_id'
    ];

    /**
     * A model belongs to a brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(){
        return $this->belongsTo('App\Brand');
    }

    /**
     * A vehicle model can only be of one type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function type(){
        return $this->belongsTo('App\Type');
    }

    /**
     * There can be multiple vehicles with same model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vehicles(){
        return $this->hasMany('App\Vehicle');
    }
}
