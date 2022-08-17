<?php

namespace App\Console\Commands;

use App\Http\Controllers\TemplateFieldValueController;
use Illuminate\Console\Command;

class GenerateContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:content {langs} {page_types}';

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
    public function handle(TemplateFieldValueController $controller)
    {
        $controller->consoleGenerate(
	        $this->argument('langs'),
	        $this->argument('page_types'),
	        $this
        );
    }
}
