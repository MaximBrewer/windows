<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unrecognized extends Model
{
    protected $fillable = ['window_title', 'car_producer_id', 'car_model', 'eurocode', 'misstake'];
    //
    public function carProducer()
    {
        return $this->belongsTo(CarProducer::class);
    }
}
