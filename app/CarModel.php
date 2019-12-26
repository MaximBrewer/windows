<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $fillable = ['title', 'car_producer_id'];

    public function carProducer()
    {
        return $this->belongsTo(CarProducer::class);
    }
    public function eurocodes()
    {
        return $this->belongsToMany(Eurocode::class, 'eurocode_car_model');
    }
}
