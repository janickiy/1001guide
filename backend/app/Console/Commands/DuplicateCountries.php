<?php

namespace App\Console\Commands;

use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryParserController;
use Illuminate\Console\Command;

class DuplicateCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicate:countries';

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
    public function handle(CountryParserController $controller)
    {
        $controller->duplicateCountries();
    }
}
