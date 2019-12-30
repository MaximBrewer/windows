<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WindowModel extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at',
        'window_producer_id',
        'car_producer_id',
        'car_model_id',
        'car_body_id',
        'window_type_id',
    ];

    protected $casts = [
        'created_at' => 'date:d.m.Y h:m:s',
        'updated_at' => 'date:d.m.Y h:m:s',
    ];

    protected $fillable = [
        'title',
        'car_model_id',
        'car_body_id',
        'window_model',
        'window_producer_id',
        'car_producer_id',
        'car_producer_id',
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

    protected $appends = [
        'window_producer_title',
        'car_producer_title',
        'car_model_title',
        'car_body_title',
        'window_type_title',
        'stores',
    ];

    public function getStoresAttribute()
    {
        $stroes = [];
        if(!!$this->tushino) $stroes[] = ["Тушино", $this->tushino];
        if(!!$this->kuncevo) $stroes[] = ["Кунцево", $this->kuncevo];
        if(!!$this->marino) $stroes[] = ["Марьино", $this->marino];
        if(!!$this->ismailovo) $stroes[] = ["Измайлово", $this->ismailovo];
        if(!!$this->mkad32km) $stroes[] = ["МКАД 32 км", $this->mkad32km];
        if(!!$this->medvedkovo) $stroes[] = ["Медведково", $this->medvedkovo];
        if(!!$this->quantity) $stroes[] = ["Кол-во", $this->quantity];
        if(!!$this->stock) $stroes[] = ["Склад", $this->stock];
        if(!!$this->skolkovo) $stroes[] = ["Сколково", $this->skolkovo];
        if(!!$this->lipeckaya) $stroes[] = ["Липецкая", $this->lipeckaya];
        return $stroes;

    }
    

    public function getWindowProducerTitleAttribute()
    {
        return isset($this->windowProducer->title) ? $this->windowProducer->title : '';
    }

    public function getCarProducerTitleAttribute()
    {
        return isset($this->carProducer->title) ? $this->carProducer->title : '';
    }

    public function getCarModelTitleAttribute()
    {
        return isset($this->carModel->title) ? $this->carModel->title : '';
    }

    public function getCarBodyTitleAttribute()
    {
        return isset($this->CarBody->title) ? $this->CarBody->title : '';
    }

    public function getWindowTypeTitleAttribute()
    {
        return isset($this->windowType->title) ? $this->windowType->title : '';
    }

    public function windowProducer()
    {
        return $this->belongsTo(WindowProducer::class);
    }

    public function windowType()
    {
        return $this->belongsTo(WindowType::class);
    }
    public function carBody()
    {
        return $this->belongsTo(CarBody::class);
    }
    public function carProducer()
    {
        return $this->belongsTo(CarProducer::class);
    }
    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
