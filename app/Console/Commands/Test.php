<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Script\GetCustomers;
use App\Http\Controllers\MasOrderController;
use App\Http\Controllers\OrderMASController;
use App\Order;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:test';

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
        $o = Order::getOne(3);
        $res = (new MasOrderController())->submitToMas($o);
        dd($res);
    }
}
