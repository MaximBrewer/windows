<?php
namespace App\Traits;

trait Avtohelp
{
    use \App\Traits\Autoparser;

    private function parseAvtohelp(){

        $worksheet = $this->spreadsheet->getActiveSheet();

        $windowProducer = "";
        $carModel = "";
        $windowType = "";

        foreach ($worksheet->getRowIterator() as $row) {

            if($row->getRowIndex() < 13) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            $window = ['provider' => 5];

            foreach($cellIterator as $cell){

                $countCells++;
                switch($countCells){
                    case 1:
                        //$window['search'] = $cell->getvalue();
                    break;
                    case 2:
                        $window['title'] = $cell->getvalue();
                    break;
                    case 3:
                        $window['quantity'] = $cell->getvalue();
                    break;
                    case 4:
                        $window['eurocode'] = $cell->getvalue();
                    break;
                    case 5:
                        $window['price_opt'] = $cell->getvalue();
                    break;
                    case 6:
                        //$window['window_type'] = $cell->getvalue();
                    break;
                }

            }

            if(!$window['eurocode'] || !$window['quantity']){


                if((string)$window['title']){
                    $wthw = trim(str_ireplace("СТЕКЛА", "", (string)$window['title']));
                    if(stristr($wthw, "ЛОБОВЫЕ")){
                        $windowType = "Лобовое";
                        $windowProducer = trim(str_ireplace("ЛОБОВЫЕ", "", $wthw));
                    }
                    if(stristr($wthw, "БОКОВЫЕ")){
                        $windowType = "Боковые стекла";
                        $windowProducer = trim(str_ireplace("БОКОВЫЕ", "", $wthw));
                    }
                }
                continue;
            }

            $window['window_type'] = $windowType;
            $window['window_producer'] = $windowProducer;

            $this->Autoparser($window);

        }

    }

}
