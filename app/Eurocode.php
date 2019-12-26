<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eurocode extends Model
{
    protected $fillable = ['title'];

    public function carModels()
    {
        return $this->belongsToMany(CarModel::class, 'eurocode_car_model');
    }
}
