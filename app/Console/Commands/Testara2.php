<?php

namespace App\Console\Commands;

use App\Observers\OrderObserver;
use App\Order;
use Illuminate\Console\Command;

class Testara2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:test2';

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
//        $controller = new CreditCardController();
//
//        $payment = $controller::cardPayment(
//            '5405010000000090',
//            '1219',
//            '123',
//            'Test',
//            10
//        );
        $a = Order::getOne(15);
        (new OrderObserver())->created($a);
    }
}
