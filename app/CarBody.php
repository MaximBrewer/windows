<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarBody extends Model
{
    protected $fillable = ['title', 'car_model_id', 'body_type_id', 'eurocode', 'doors'];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
