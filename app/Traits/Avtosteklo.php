<?php
namespace App\Traits;

trait Avtosteklo
{
    use \App\Traits\Autoparser;

    private function parseAvtosteklo(){

        $worksheet = $this->spreadsheet->getActiveSheet();

        $carProducer = 0;
        $carModel = 0;
        $windowType = 0;

        foreach ($worksheet->getRowIterator() as $row) {

            if($row->getRowIndex() < 5) continue;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            $window = ['provider' => 4, 'price_opt' => 0];

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
                        $window['year'] = $cell->getvalue();
                    break;
                    case 4:
                        $window['eurocode'] = $cell->getvalue();
                    break;
                    case 5:
                        $window['window_type'] = $cell->getvalue();
                    break;
                    case 6:
                        $window['quantity'] = $cell->getvalue();
                    break;
                    case 7:
                        $window['price_opt'] = $cell->getvalue();
                    break;
                    case 8:
                        $window['window_producer'] = $cell->getvalue();
                    break;
                    case 9:
                        $window['size'] = $cell->getvalue();
                    break;
                }

            }

            if(!(string)$window['title']) continue;

            $this->Autoparser($window);

        }

    }

}
