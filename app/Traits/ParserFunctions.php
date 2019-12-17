<?php

namespace App\Traits;

// use Illuminate\Http\Request;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use Ixudra\Curl\Facades\Curl;
// use Illuminate\Support\Facades\DB;

trait ParserFunctions
{
    use \App\Traits\Steklocar;
    use \App\Traits\Skolkovo;
    use \App\Traits\Avtoglass;
    use \App\Traits\Avtohelp;
    use \App\Traits\Avtosteklo;
    use \App\Traits\XZ;

    function mb_str_ireplace($search, $replace, $subject) {

        if(is_array($subject)) {
             $ret = array();
                foreach($subject as $key => $val) {
                $ret[$key] = mb_str_ireplace($search, $replace, $val);
            }
            return $ret;
        }

        foreach((array) $search as $key => $s) {

            if($s == '') continue;
            $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_stripos($subject, $s);
            while($pos !== false) {
                $subject = mb_substr($subject, 0, $pos) . $r . mb_substr($subject, $pos + mb_strlen($s));
                $pos = mb_stripos($subject, $s, $pos + mb_strlen($r));
            }
            
        }

        return $subject;

    }

    private function parseFile(){

        if(!is_file($this->attachments_file)) return false;

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->attachments_file);
        $reader->setReadDataOnly(TRUE);
        $this->spreadsheet = $reader->load($this->attachments_file);

        $providerId = $this->getProviderByFile();

        switch ($providerId) {
            case 1:
                $this->parseSteklocar(); // Стеклокар
            break;
            case 2:
                $this->parseSkolkovo(); // Сколково
            break;
            case 3:
                $this->parseAvtoglass(); //Автогласс
            break;
            case 4:
                $this->parseAvtosteklo(); // Avtosteklo
            break;
            case 5:
                $this->parseAvtohelp(); // АВТО-ХЭЛП М
            break;
            case 6:
                $this->parseXZ(); // Иномарки легковые
            break;
            default:
                return false;
        }

    }

    private function getProviderByFile()
    {

        $bot = new \App\TelegramBot();

        $worksheet = $this->spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $countCells = 0;
            foreach($cellIterator as $cell){

                $countCells++;
                $srt = strval($cell->getValue());

                if($srt){

                    if($countCells == 12 && $srt == 'Оптовая') return 1; // Стеклокар
                    if($countCells == 11 && $srt == 'Склад') return 2; // Сколково
                    if($countCells == 4 && $srt == 'Планета Авто Гласс') return 3; //Автогласс
                    if($countCells == 9 && $srt == 'Размер') return 4; // Avtosteklo
                    if($countCells == 2 && strstr("АВТО-ХЭЛП", $srt)) return 5; // АВТО-ХЭЛП М
                    if($countCells == 2 && $srt == 'прайс лист для ') return 6; // XZ
                }

            }

        }

        $bot->sendMessage('Произошла ошибка при определении формата файла в парсере XLS');

        session()->flash('message', "Не удалось определить формат выгрузки");
        return redirect('admin/');


    }

    public function mail(){

        set_time_limit(999999);
        $user = 'testrecievemail@gmail.com';
        $password = 'qqaazz123QQAAZZ';
        $connect_to = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $inbox = \imap_open($connect_to,$user,$password);

        if(!$inbox) {
            session()->flash('error', 'Cannot connect to Gmail: ' . \imap_last_error());
            return redirect('admin/');
        }

        $emails = \imap_search($inbox, 'UNSEEN');

        if($emails) {
            $count = 1;
            rsort($emails);
            foreach($emails as $email_number)
            {
                $overview = \imap_fetch_overview($inbox,$email_number,0);
                $message = \imap_fetchbody($inbox,$email_number,2);
                $structure = \imap_fetchstructure($inbox, $email_number);
                $attachments = array();
                if(isset($structure->parts) && count($structure->parts))
                {
                    for($i = 0; $i < count($structure->parts); $i++)
                    {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if($structure->parts[$i]->ifdparameters)
                        {
                            foreach($structure->parts[$i]->dparameters as $object)
                            {
                                if(strtolower($object->attribute) == 'filename')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if($structure->parts[$i]->ifparameters)
                        {
                            foreach($structure->parts[$i]->parameters as $object)
                            {
                                if(strtolower($object->attribute) == 'name')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if($attachments[$i]['is_attachment'])
                        {
                            $attachments[$i]['attachment'] = \imap_fetchbody($inbox, $email_number, $i+1);
                            if($structure->parts[$i]->encoding == 3)
                            {
                                $attachments[$i]['attachment'] = \base64_decode($attachments[$i]['attachment']);
                            }
                            elseif($structure->parts[$i]->encoding == 4)
                            {
                                $attachments[$i]['attachment'] = \quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
                $counter = 1;
                foreach($attachments as $attachment)
                {
                    if($attachment['is_attachment'] == 1)
                    {
                        $filename = $attachment['name'];
                        if(empty($filename)) $filename = $attachment['filename'];

                        if(empty($filename)) $filename = \microtime() . ".dat";
                        if(!\is_dir($this._attachments_folder))
                        {
                            \mkdir($this._attachments_folder);
                        }
                        $filename = $counter . ".xls";
                        $fp = \fopen($this._attachments_folder ."/". $email_number . "-" . $filename, "w+");
                        \fwrite($fp, $attachment['attachment']);
                        \fclose($fp);
                    }
                }
                break;
            }
        }
        imap_close($inbox);
    }
}
