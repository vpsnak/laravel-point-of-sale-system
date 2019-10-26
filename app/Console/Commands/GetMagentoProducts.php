<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Script\ProductSync;
use Illuminate\Console\Command;

class GetMagentoProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:get:products';

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
        ProductSync::getFromMagento(true);
    }
}
