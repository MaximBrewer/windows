<?php
namespace App\Traits;
use Illuminate\Support\Facades\DB;

trait XZ
{

    private function parseXZ(){

        $worksheet = $this->spreadsheet->getActiveSheet();

        $carProducer = 0;
        $carModel = 0;
        $windowType = 0;

        foreach ($worksheet->getRowIterator() as $row) {

            if($row->getRowIndex() < 10) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            $window = ['provider' => 6, 'char' => [], 'eurocode' => []];

            foreach($cellIterator as $cell){

                $countCells++;
                switch($countCells){
                    case 1:
                        $window['car_producer'] = $cell->getvalue();
                    break;
                    case 2:
                        $window['article'] = $cell->getvalue();
                    break;
                    case 3:
                        $window['eurocode_string'] = $cell->getCalculatedValue();
                    break;
                    case 4:
                        $window['eurocode'][] = $cell->getvalue();
                    break;
                    case 5:
                        $window['eurocode'][] = $cell->getvalue();
                    break;
                    case 6:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 7:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 8:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 9:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 10:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 11:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 12:
                        //$window['eurocode'][] = $cell->getvalue();
                    break;
                    case 13:
                        $window['title'] = $cell->getvalue();
                    break;
                    case 14:
                        $window['car_body'] = $cell->getvalue();
                    break;
                    case 15:
                        $window['year'] = $cell->getvalue();
                    break;
                    case 16:
                        if($cell->getvalue()) $window['char'][] = "шелк";
                    break;
                    case 17:
                        if($cell->getvalue()) $window['char'][] = "vin";
                    break;
                    case 18:
                        if($cell->getvalue()) $window['char'][] = "пятак";
                    break;
                    case 19:
                        if($cell->getvalue()) $window['char'][] = "д/д";
                    break;
                    case 20:
                        if($cell->getvalue()) $window['char'][] = "без дд";
                    break;
                    case 21:
                        $window['size'] = $cell->getvalue();
                    break;
                    case 22:
                        $window['price_opt'] = $cell->getvalue();
                    break;
                }

            }

            if(!(string)$window['title']){
                $carProducerStr = (string)$window['car_producer'];
                $carProducer = \App\CarProducer::where('title', (string)$window['car_producer'])->first();
                if(!$carProducer) $carProducer = \App\CarProducer::create(['title' => (string)$window['car_producer']]);
                continue;

            }


            $windowEurocodeTypes = [
                'A' => 'Лобовое',
                'B' => 'Заднее стекло',
                'F' => 'Боковое стекло',
                'L' => 'Боковое стекло левое',
                'R' => 'Боковое стекло правое'
            ];

            if($flo = substr($window['eurocode'][1], 0, 1) and isset($windowEurocodeTypes[$flo])){


            $window['window_type'] = $windowEurocodeTypes[substr($window['eurocode'][1], 0, 1)];

                $windowType = \App\WindowType::where('title', (string)$window['window_type'])->first();
                if(!$windowType) $windowType = \App\WindowType::create(['title' => (string)$window['window_type']]);
                $window['window_type'] = $windowType->id;

            }

            $window['eurocode'] = $window['eurocode_string'];
            unset($window['eurocode_string']);

            $window['char'] = implode(", ", $window['char']);

            $carModels = $this->mb_str_ireplace($carProducerStr, "", (string)$window['title']);
            $carModels = explode("/", trim($carModels));


            $carModel = \App\CarModel::where('title', (string)$window['car_model'])->first();
            if(!$carModel) $carModel = \App\CarModel::create(['title' => (string)$window['car_model'], 'car_producer' => $carProducer->id]);

            $window['car_model'] = $carModel->id;
            $window['car_producer'] = $carProducer->id;

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
