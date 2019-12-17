<?php

namespace App\Http\Controllers;

use App\Unknown;
use App\CarProducer;
use App\CarModel;
use Illuminate\Http\Request;

class UnrecognizedController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->post('unrecognized') && is_array($request->post('unrecognized'))) {
            foreach ($request->post('unrecognized') as $unrecognized_id => $car_producer_ids) {
                foreach ($car_producer_ids as $car_producer_id => $model_titles) {
                    if (intval($car_producer_id)) {
                        foreach ($model_titles as $model_title) {
                            if (!empty($model_title)) {
                                if ($carProducer = CarProducer::find($car_producer_id)) {
                                    $carModel = CarModel::where('car_producer', $carProducer->id)
                                        ->where('title', $model_title)
                                        ->first();
                                    if (!$carModel) {
                                        CarModel::create([
                                            'title' => $model_title,
                                            'car_producer' => $carProducer->id
                                        ]);
                                    }
                                }
                                $Unknown = Unknown::find($unrecognized_id);
                                $Unknown->delete();
                            }
                        }
                    }
                }
            }
        }
        $unrecognizeds = Unknown::all();
        return view('unrecognized', ['unrecognizeds' => $unrecognizeds, 'i' => 0]);
    }
}