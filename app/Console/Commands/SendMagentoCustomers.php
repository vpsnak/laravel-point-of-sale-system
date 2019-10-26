<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Script\CustomerSync;
use Illuminate\Console\Command;

class SendMagentoCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:send:customers';

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
        CustomerSync::sendToMagento();
    }
}
