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
            $window['window_producer_id'] = (int)$wp->id;

        } else return false;

        $carProducers = DB::select(
            "SELECT id FROM car_producers where LOCATE(title, ?) > 0;", [$window['title']]
        );

        $carProducersIds = [];
        foreach($carProducers as $carProducer)
            $carProducersIds[] = $carProducer->id;

        if(isset($window['window_type']) && (string)$window['window_type']){

            $windowType = \App\WindowType::where('title', (string)$window['window_type'])->first();
            if(!$windowType) $windowType = \App\WindowType::create(['title' => (string)$window['window_type']]);
            $window['window_type_id'] = (int)$windowType->id;

        } else {

            $window['window_type_id'] = null;

        }

        $done = false;

        if($window['eurocode']) {

            $exists = DB::select(
                "SELECT DISTINCT car_body_id FROM window_models where LOCATE(eurocode, ?) > 0 AND provider = 1;", [$window['eurocode']]
            );

            foreach($exists as $exist){

                if(isset($exist->car_body_id) && $exist->car_body_id){

                    $carBody = \App\CarBody::find($exist->car_body_id);

                    $window_model = \App\WindowModel::
                        where('title', (string)$window['title'])->
                        where('window_producer_id', $window['window_producer_id'])->
                        where('window_type_id', $window['window_type'])->
                        where('car_body_id', $carBody->id)->
                        where('provider', $window['provider'])->
                        first();

                    $window['car_producer_id'] = $carBody->carModel->carProducer->id;
                    $window['car_model_id'] = $carBody->carModel->id;
                    $window['car_body_id'] = $carBody->id;
                    
                    if(!$window_model) $window_model = new \App\WindowModel();
                    $window_model->fill($window);
                    $window_model->save();

                    $done = true;

                }
            }

            if(!$done && count($carProducersIds)) {
            
                foreach(\App\Eurocode::where('title', substr($window['eurocode'], 0, 4))->cursor() as $eurocode){

                    foreach($eurocode->carModels as $carModel){

                        if(in_array($carModel->carProducer->id, $carProducersIds)){
                            
                            $window_model = \App\WindowModel::
                                where('title', (string)$window['title'])->
                                where('car_producer_id', $carModel->carProducer->id)->
                                where('car_model_id', $carModel->id)->
                                where('window_producer_id', $window['window_producer_id'])->
                                where('window_type_id', $window['window_type_id'])->
                                where('provider', $window['provider'])->
                                first();

                            $window['car_producer_id'] = $carModel->carProducer->id;
                            $window['car_model_id'] = $carModel->id;
                            
                            if(!$window_model) $window_model = new \App\WindowModel();
                            $window_model->fill($window);
                            $window_model->save();

                            $done = true;

                        }

                    }
                    
                }

            }

        }



        if(!$done && $window['title']){


            $unrecognizedObj = new \App\Unrecognized();
            $unrecognized = $unrecognizedObj
                ->where('window_title', $window['title'])
                ->where('eurocode', $window['eurocode'])
                ->where('misstake', 'window')
                ->first();
            if (!$unrecognized) $unrecognized = $unrecognizedObj->create([
                'window_title' => $window['title'],
                'eurocode' => $window['eurocode'],
                'misstake' => 'window'
            ]);

        }

    }

}
