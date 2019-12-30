<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\WindowModel;
use App\CarProducer;
use App\WindowType;
use App\CarModel;
use App\CarBody;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WindowsController extends Controller
{

    private $perPage = 20;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex()
    {
        return view('manager/index');
    }

    public function getData(Request $request)
    {
        $attributes = [];

        if((string)$request->post('window_title')){
            $windows = WindowModel::where('title', "like", "%" . $request->post('window_title') . "%")->paginate($this->perPage);
        }elseif((string)$request->post('eurocode')){
            $windows = WindowModel::where('eurocode', "like", $request->post('eurocode') . "%")->paginate($this->perPage);
        }else{
            if((int)$request->post('car_body_id')){
                $attributes['car_body_id'] = $request->post('car_body_id');
            }elseif((int)$request->post('car_model_id')){
                $attributes['car_model_id'] = $request->post('car_model_id');
            }elseif((int)$request->post('car_producer_id')){
                $attributes['car_producer_id'] = $request->post('car_producer_id');
            }
            if((int)$request->post('window_type_id')){
                $attributes['window_type_id'] = $request->post('window_type_id');
            }
            $windows = WindowModel::where($attributes)->paginate($this->perPage);
        }

        //var_dump($attributes);die;

        $windows->withPath(url()->full());
        
        return response()->json($windows);
    }

    public function getCarProducers()
    {
        $carProducers = array_merge([["id" => 0, "title" => "Все"]], CarProducer::all()->toArray());
        return response()->json($carProducers);
    }

    public function getWindowTypes()
    {
        $windowTypes = array_merge([["id" => 0, "title" => "Все"]], WindowType::all()->toArray());
        return response()->json($windowTypes);
    }

    public function getCarModels(Request $request)
    {
        $carModels = array_merge([["id" => 0, "title" => "Все"]], CarModel::where('car_producer_id', $request->post('car_producer_id'))->get()->toArray());
        return response()->json($carModels);
    }

    public function getCarBodies(Request $request)
    {
        $carBodies = array_merge([["id" => 0, "title" => "Все"]], CarBody::where('car_model_id', $request->post('car_model_id'))->get()->toArray());
        return response()->json($carBodies);
    }
}
