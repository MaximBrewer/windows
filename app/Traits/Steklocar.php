<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;


trait Steklocar
{
    private function parseModel($string)
    {
        $matches = [];
        preg_match('~^[0-9A-Z\,\.\/\-\s\(\)]*(\([0-9]{0,4}-[0-9]{0,4}\))$~', $string, $matches);

        if (isset($matches[1])) {
            $years = $matches[1];
            $string = trim(str_ireplace($years, "", $string));
        }

        $bodyTypes = DB::select(
            "SELECT id, title, code FROM body_types where LOCATE(`code`, ?) > 0;",
            [$string]
        );

        $bodyType = null;

        if (!empty($bodyTypes)) {
            $bodyType = $bodyTypes[0];
            foreach ($bodyTypes as $bodyTypee) {
                $bodyType = strlen($bodyTypee->code) > strlen($bodyType->code) ? $bodyTypee : $bodyType;
            }
        } else {
            return false;
        }

        $matches = [];
        $string = trim(str_ireplace($bodyType->code, "", $string));

        preg_match('/^.*([0-9]{1}D)$/', $string, $matches);

        if (!empty($matches)) {
            $doors = $matches[1];
            $string = trim(str_ireplace($doors, "", $string));
        } else {
            return false;
        }

        return [
            "model" => $string,
            "doors" => $doors,
            "body" => $bodyType->code,
            "years" => $years,
        ];
    }

    private function parseSteklocar()
    {

        $worksheet = $this->spreadsheet->getActiveSheet();

        $carProducer = 0;
        $carModel = 0;
        $windowType = 0;

        $ft = [];

        foreach ($worksheet->getRowIterator() as $row) {

            if ($row->getRowIndex() < 3) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            $window = ['provider' => 1];

            foreach ($cellIterator as $cell) {

                $countCells++;
                switch ($countCells) {
                    case 1:
                        $window['window_producer'] = $cell->getvalue();
                        break;
                    case 2:
                        $window['title'] = $cell->getvalue();
                        break;
                    case 3:
                        $window['eurocode'] = $cell->getvalue();
                        break;
                    case 4:
                        $window['type'] = $cell->getvalue();
                        break;
                    case 5:
                        $window['tushino'] = $cell->getvalue();
                        break;
                    case 6:
                        $window['kuncevo'] = $cell->getvalue();
                        break;
                    case 7:
                        $window['marino'] = $cell->getvalue();
                        break;
                    case 8:
                        $window['ismailovo'] = $cell->getvalue();
                        break;
                    case 9:
                        $window['mkad32km'] = $cell->getvalue();
                        break;
                    case 10:
                        $window['medvedkovo'] = $cell->getvalue();
                        break;
                    case 11:
                        $window['price_install'] = $cell->getvalue();
                        break;
                    case 12:
                        $window['price_opt'] = $cell->getvalue();
                        break;
                }
            }

            if (
                !(string) $window['eurocode']
                && !(string) $window['window_producer']
                && !(string) $window['price_opt']
            ) {

                $ft[] = (string) $window['title'];
                continue;
            }

            if (count($ft) > 2) {
                $f = $ft;
                $carProducer = \App\CarProducer::where('title', $f[0])->first();
                if (!$carProducer) $carProducer = \App\CarProducer::create(['title' => $f[0]]);

                $carModels = DB::select(
                    "SELECT id, title, car_producer FROM car_models where car_producer = ? AND LOCATE(`title`, ?) > 0;",
                    [
                        $carProducer->id,
                        $f[1]
                    ]
                );

                $carModel = null;


                if (is_array($carModels) && count($carModels)) {
                    $carModel = $carModels[0];
                    foreach ($carModels as $carModele) {
                        $carModel = strlen($carModele->title) > strlen($carModel->title) ? $carModele : $carModel;
                    }
                }


                if (!$carModel) {
                    $unknownObj = new \App\Unknown();
                    $unknown = $unknownObj
                        ->where('car_model', $f[1])
                        ->where('car_producer_id', $carProducer->id)
                        ->where('misstake', 'model')
                        ->first();
                    if (!$unknown) $unknown = $unknownObj->create([
                        'car_model' => $f[1],
                        'car_producer_id' => $carProducer->id,
                        'misstake' => 'model'
                    ]);
                } else {

                    $car_body = trim(str_ireplace($carModel->title, "", $f[1]));

                    $carBodyObj = new \App\CarBody();
                    $carBody = $carBodyObj
                        ->where('car_model', $carModel->id)
                        ->where('title', $car_body)
                        ->first();
                    if (!$carBody) $carBody = $carBodyObj->create([
                        'title' => $car_body,
                        'car_model' => $carModel->id,
                    ]);

                    $windowType = \App\WindowType::where('title', $f[2])->first();
                    if (!$windowType) $windowType = \App\WindowType::create(['title' => $f[2]]);
                }
            } elseif (count($ft) > 1) {

                $f = [$f[0], $ft[0], $ft[1]];

                $carModels = DB::select(
                    "SELECT id, title, car_producer FROM car_models where car_producer = ? AND LOCATE(`title`, ?) > 0;",
                    [
                        $carProducer->id,
                        $f[1]
                    ]
                );

                $carModel = null;

                if (is_array($carModels) && count($carModels)) {
                    $carModel = $carModels[0];
                    foreach ($carModels as $carModele) {
                        $carModel = strlen($carModele->title) > strlen($carModel->title) ? $carModele : $carModel;
                    }
                }

                if (!$carModel) {
                    $unknownObj = new \App\Unknown();
                    $unknown = $unknownObj
                        ->where('car_model', $f[1])
                        ->where('car_producer_id', $carProducer->id)
                        ->where('misstake', 'model')
                        ->first();
                    if (!$unknown) $unknown = $unknownObj->create([
                        'car_model' => $f[1],
                        'car_producer_id' => $carProducer->id,
                        'misstake' => 'model'
                    ]);
                } else {

                    $car_body = trim(str_ireplace($carModel->title, "", $f[1]));

                    $carBodyObj = new \App\CarBody();
                    $carBody = $carBodyObj
                        ->where('car_model', $carModel->id)
                        ->where('title', $car_body)
                        ->first();
                    if (!$carBody) $carBody = $carBodyObj->create([
                        'title' => $car_body,
                        'car_model' => $carModel->id,
                    ]);

                    $windowType = \App\WindowType::where('title', (string) $f[2])->first();
                    if (!$windowType) $windowType = \App\WindowType::create(['title' => (string) $f[2]]);
                }
            } elseif (count($ft) > 0) {
                $f[2] = $ft[0];
                $windowType = \App\WindowType::where('title', (string) $f[2])->first();
                if (!$windowType) $windowType = \App\WindowType::create(['title' => (string) $f[2]]);
            }

            $ft = [];

            if (!$carModel || !$carProducer || !$carBody || !$windowType) continue;

            $wp = \App\WindowProducer::where('title', (string) $window['window_producer'])->first();
            if (!$wp) $wp = \App\WindowProducer::create(['title' => (string) $window['window_producer']]);

            $window['window_producer'] = $wp->id;
            $window['window_type'] = $windowType->id;
            $window['car_model'] = $carModel->id;
            $window['car_body'] = $carBody->id;

            var_dump($window);

            $window_model = \App\WindowModel::where('title', (string) $window['title'])->where('provider', $window['provider'])->first();

            if (!$window_model) $window_model = new \App\WindowModel();

            $window_model->fill($window);
            $window_model->save();
        }
    }
}
