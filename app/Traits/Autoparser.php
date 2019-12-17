<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;

trait Autoparser
{

    private function Autoparser($window){

        $window['price_opt'] = preg_replace("/[^0-9\,\.]/", '', $window['price_opt']);
        $window['price_opt'] = floatval(str_replace(",", '.', $window['price_opt']));

        if(isset($window['window_producer']) && (string)$window['window_producer']){

            $wp = \App\WindowProducer::where('title', (string)$window['window_producer'])->first();
            if(!$wp) $wp = \App\WindowProducer::create(['title' => (string)$window['window_producer']]);
            $window['window_producer'] = (int)$wp->id;

        }

        $window['window_producer'] = (int)$window['window_producer'];

        if(isset($window['window_type']) && (string)$window['window_type']){

            $windowType = \App\WindowType::where('title', (string)$window['window_type'])->first();
            if(!$windowType) $windowType = \App\WindowType::create(['title' => (string)$window['window_type']]);
            $window['window_type'] = (int)$windowType->id;

        }

        $window['window_type'] = (int)$window['window_type'];

        $done = false;
        
        foreach(\App\Eurocode::where('eurocode', substr($window['eurocode'], 0, 4))->cursor() as $eurocode){

            if($eurocode['car_model']){

                $window['car_model'] = $eurocode['car_model'];
                $window['car_producer'] = \App\CarModel::where('id', (string)$window['car_model'])->first()->car_producer;

                $window_model = \App\WindowModel::
                    where('title', (string)$window['title'])->
                    where('provider', $window['provider'])->
                    first();

                if(!$window_model) $window_model = new \App\WindowModel();
                $window_model->fill($window);
                $window_model->save();

                $done = true;

            }

        }
        if(!$done){

            $carProducers = DB::select(
                "SELECT id, title FROM car_producers where LOCATE(title, ?) > 0;", [$window['title']]
            );

            foreach($carProducers as $carProducer){

                $wmodel = trim(str_ireplace($carProducer->title, "", $window['title']), ",. ");

                $carModels = DB::select(
                    "SELECT id, title FROM car_models where car_producer = ? AND LOCATE(title, ?) > 0;",
                    [
                        $carProducer->id,
                        $wmodel
                    ]
                );

                $window['car_producer'] = $carProducer->id;

                foreach($carModels as $carModel){

                    $window['car_model'] = $carModel->id;

                    $window_model = \App\WindowModel::
                        where('title', (string)$window['title'])->
                        where('provider', $window['provider'])->
                        first();

                    if(!$window_model) $window_model = new \App\WindowModel();

                    $window_model->fill($window);
                    $window_model->save();

                }

            }

        }

    }

}
