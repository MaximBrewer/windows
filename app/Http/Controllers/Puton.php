<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Puton extends Controller
{

    public function __invoke(Request $request)
    {

        $models = $request->post('model');

        if ($models && !empty($models)) {

            foreach ($models as $model) {
                if (empty($model['producer']) || empty($model['model'])) continue;
                $carModel = new \App\carModel();
                $carModel->car_producer = $model['producer'];
                $carModel->title = $model['model'];
                $carModel->save();
            }
        }

        $eurocodes = DB::table('eurocode')->select('car_producer', 'car_model', 'eurocode')->get();

        $i = 0;

        foreach ($eurocodes as $eurocode) {

            $carProducer = \App\CarProducer::where('title', (string) $eurocode->car_producer)->first();

            if (!$carProducer) {
                $unkn = new \App\Unknown();
                if (
                    empty($unkn
                        ->where('car_producer', (string) $eurocode->car_producer)
                        ->where('car_model', (string) $eurocode->car_model)
                        ->where('eurocode', (string) $eurocode->eurocode)
                        ->first())
                ) {
                    $unkn->car_producer = $eurocode->car_producer;
                    $unkn->car_model = $eurocode->car_model;
                    $unkn->eurocode = $eurocode->eurocode;
                    $unkn->misstake = 'producer';
                    $unkn->save();
                }
                continue;
            }

            $carModels = DB::select(
                "SELECT id, title FROM car_models where car_producer = ? AND LOCATE(title, ?) > 0;",
                [
                    $carProducer->id,
                    (string) $eurocode->car_model
                ]
            );

            if (empty($carModels)) {
                $unkn = new \App\Unknown();
                if (
                    empty($unkn
                        ->where('car_producer', (string) $eurocode->car_producer)
                        ->where('car_model', (string) $eurocode->car_model)
                        ->where('eurocode', (string) $eurocode->eurocode)
                        ->first())
                ) {
                    $unkn->car_producer = $eurocode->car_producer;
                    $unkn->car_model = $eurocode->car_model;
                    $unkn->eurocode = $eurocode->eurocode;
                    $unkn->misstake = 'model';
                    $unkn->save();
                }
                continue;
            }


            $carModel = $carModels[0];

            foreach ($carModels as $carModele) {
                $carModel = strlen($carModele->title) > strlen($carModel->title) ? $carModele : $carModel;
            }

            $matches = [];

            $txt = trim(str_ireplace($carModel->title, "", (string) $eurocode->car_model));

            $txt = str_replace("( ", "(", $txt);
            $txt = str_replace(" )", ")", $txt);

            preg_match('~^[0-9A-Z\,\.\/\-\s\(\)]*(\([0-9]{0,4}-[0-9]{0,4}\))$~', $txt, $matches);

            if (isset($matches[1])) {

                $years = $matches[1];
                $years = str_replace([")", "("], "", $years);
                $start = explode("-", $years)[0];
                $finish = explode("-", $years)[1];


                $txt = trim(str_ireplace($matches[1], "", $txt));
            }

            $txt = str_replace([")", "("], "", $txt);

            $bodyTypes = DB::select(
                "SELECT id, title, code FROM body_types where LOCATE(`code`, ?) > 0;",
                [
                    $txt
                ]
            );

            $bodyType = null;
            $body_type = false;

            if (!empty($bodyTypes)) {

                // if(
                //     empty(
                //         $unkn
                //             ->where('car_producer', (string) $eurocode->car_producer)
                //             ->where('car_model', (string) $eurocode->car_model)
                //             ->where('eurocode', (string) $eurocode->eurocode)
                //             ->first()
                //     )
                // ){
                //     $unkn->car_producer = $eurocode->car_producer;
                //     $unkn->car_model = $eurocode->car_model;
                //     $unkn->eurocode = $eurocode->eurocode;
                //     $unkn->misstake = 'model';
                //     $unkn->save();
                // }
                // var_dump($txt);
                // echo "<br>";
                // var_dump($eurocode->car_model);
                // echo "<br>";
                // var_dump($carModel->title);
                // echo "<br>";
                // var_dump($eurocode->car_producer);
                // echo "<br>";
                // echo "--------------------------";

                $bodyType = $bodyTypes[0];
                foreach ($bodyTypes as $bodyTypee) {
                    $bodyType = strlen($bodyTypee->code) > strlen($bodyType->code) ? $bodyTypee : $bodyType;
                }
            }

            $body = $txt;

            if ($bodyType) {
                $body = trim(str_ireplace($bodyType->code, "", $body));
                $body_type = $bodyType->id;
            }

            if ($body) {
                $i++;

                preg_match('/^(.*)([0-9]+D)$/', $body, $matches);

                if (!empty($matches)) {

                    $carBody = new \App\CarBody();
                    if (
                        empty($carBody
                            ->where('car_model', $carModel->id)
                            ->where('eurocode', $eurocode->eurocode)
                            ->first())
                    ) {
                        $carBody->title = $matches[1];
                        $carBody->doors = str_ireplace("D", "", $matches[2]);
                        $carBody->car_model = $carModel->id;
                        $carBody->eurocode = $eurocode->eurocode;
                        $carBody->body_type = $body_type;
                        $carBody->save();
                    }
                } else {
                    echo "<pre>";
                    var_dump($eurocode->car_model);
                    var_dump($body);
                    var_dump($txt);
                    var_dump($bodyType);
                    echo "</pre>";
                }
            } else {

                $unkn = new \App\Unknown();
                // if(
                //     empty(
                //         $unkn
                //             ->where('car_producer', (string) $eurocode->car_producer)
                //             ->where('car_model', (string) $eurocode->car_model)
                //             ->where('eurocode', (string) $eurocode->eurocode)
                //             ->first()
                //     )
                // ){
                $unkn->car_producer = $eurocode->car_producer;
                $unkn->car_model = $eurocode->car_model;
                $unkn->eurocode = $eurocode->eurocode;
                $unkn->misstake = 'body';
                $unkn->save();
                // }

            }
        }

        var_dump($i);
        die;

        // $handle = @fopen($_SERVER['DOCUMENT_ROOT'] . "/csv.csv", 'r');
        // if ($handle) {
        //     while (($buffer = fgets($handle, 4096)) !== false) {

        //         $car = explode(";", $buffer);

        //         $carProducer = \App\CarProducer::where('title', $car[0])->first();
        //         if(!$carProducer) $carProducer = \App\CarProducer::create(['title' => $car[0]]);
        //         $carProducerId = $carProducer->id;

        //         $CarModel = \App\CarModel::where('title', $car[1])->first();
        //         if(!$CarModel)
        //             $CarModel = \App\CarModel::create([
        //                 'title' => $car[1],
        //                 'car_producer' => $carProducerId,
        //                 'year_start' => intval($car[2]) ? $car[2] : null,
        //                 'year_finish' => intval($car[3]) ? $car[3] : null
        //             ]);
        //         else
        //         {
        //             // var_dump([
        //             //     'title' => $car[1],
        //             //     'car_producer' => $carProducerId,
        //             //     'year_start' => intval($car[2]) ? $car[2] : null,
        //             //     'year_finish' => intval($car[3]) ? $car[3] : null
        //             // ]);
        //             // die;
        //             $CarModel->update([
        //                 'title' => $car[1],
        //                 'car_producer' => $carProducerId,
        //                 'start_year' => intval($car[2]) ? $car[2] : null,
        //                 'finish_year' => intval($car[3]) ? $car[3] : null
        //             ]);
        //         }
        //         $carProducerId = $CarModel->id;

        //     }
        //     if (!feof($handle)) {
        //         echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
        //     }
        //     fclose($handle);
        // }
        // die;
        return view('home');
    }
}
