<?php

namespace App\Console\Commands;

use App\Http\Controllers\TagController;
use Illuminate\Console\Command;

class UpdateTagTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:totals';

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
    public function handle(TagController $controller)
    {
        $controller->updateTotals($this);
    }
}
