<?php
namespace App\Traits;

trait Avtoglass
{
    use \App\Traits\Autoparser;

    private function parseAvtoglass(){

        $worksheet = $this->spreadsheet->getActiveSheet();

        $carProducer = 0;
        $carModel = 0;
        $windowType = 0;

        foreach ($worksheet->getRowIterator() as $row) {

            if($row->getRowIndex() < 10) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            $window = ['provider' => 3];

            foreach($cellIterator as $cell){

                $countCells++;
                switch($countCells){
                    case 1:
                        $window['car_producer'] = $cell->getvalue();
                    break;
                    case 2:
                        $window['window_type'] = $cell->getvalue();
                    break;
                    case 3:
                        $window['window_producer'] = $cell->getvalue();
                    break;
                    case 4:
                        $window['title'] = $cell->getvalue();
                    break;
                    case 5:
                        $window['year'] = $cell->getvalue();
                    break;
                    case 6:
                        $window['eurocode'] = $cell->getvalue();
                    break;
                    case 7:
                        $window['size'] = $cell->getvalue();
                    break;
                    case 8:
                        $window['price_opt'] = $cell->getvalue();
                    break;
                    case 9:
                        //$window['char'] = $cell->getvalue();
                    break;
                    case 10:
                        $window['quantity'] = $cell->getvalue();
                    break;
                }

            }

            if(!(string)$window['title']) continue;

            $this->Autoparser($window);

        }

    }

}
