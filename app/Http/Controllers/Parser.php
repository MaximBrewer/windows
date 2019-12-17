<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symphony\Component\Process\Process;

class Parser extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    var $attachments_folder;
    var $attachments_file;
    var $spreadsheet;



    public function __invoke(Request $request)
    {

        system('php ' . dirname(dirname(dirname(dirname(__FILE__)))) . '/artisan parser:start > /dev/null 2>&1 &');
        session()->flash('message', "Загрузка запущена");
        return redirect('admin/');

    }

}
