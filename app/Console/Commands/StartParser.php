<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartParser extends Command
{
    use \App\Traits\ParserFunctions;

    var $attachments_folder;
    var $attachments_file;
    var $spreadsheet;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->attachments_folder = dirname(dirname(dirname(dirname(__FILE__)))) . "/attachments";

        @mkdir($this->attachments_folder);

        //$this->mail();

        $counter = 0;
        $files = glob($this->attachments_folder . '/*');
        foreach ($files as $file) {

            if (!is_file($file)) continue;
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if (strtolower($ext) != 'xls' && strtolower($ext) != 'xlsx') {
                unlink($file);
                continue;
            }

            $counter++;

            $this->attachments_file = $file;
            $this->parseFile();
        }
    }
}
