<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WindowModel extends Model
{
    protected $fillable = [
        'title',
        'car_model_id',
        'car_body_id',
        'window_model',
        'window_producer_id',
        'window_type_id',
        'eurocode',
        'type',
        'tushino',
        'kuncevo',
        'marino',
        'ismailovo',
        'mkad32km',
        'medvedkovo',
        'price_install',
        'price_opt',
        'provider',
        'quantity',
        'size',
        'time',
        'year',
        'spec',
        'char',
        'stock',
        'skolkovo',
        'lipeckaya'
    ];
    public function windowProducer()
    {
        return $this->belongsTo(WindowProducer::class);
    }
    public function carProducer()
    {
        return $this->belongsTo(CarProducer::class);
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
    public function carBody()
    {
        return $this->belongsTo(CarBody::class);
    }
}
