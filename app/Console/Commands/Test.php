<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Script\GetCustomers;
use App\Http\Controllers\OrderMASController;
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
        //4263970000005262
        // 161119ED4-08590ADA-4951-46C8-8DD1-17C0EB5C502F
//        $o = Order::getOne(3);
//        $res = (new MasOrderController())->submitToMas($o);
//        $res = (new CreditCardController)->cardPayment(
//            '4159288888888882',
//            '1222',
//            '123',
//            'asdasdas',
//            50
//        );
//        print_r($res);

        //161119ED4-34736AA0-F7C8-4E01-A5FF-317377C1D6CF
//        $res = (new CreditCardController)->transactionAction( 'txnquery',['ssl_txn_id'=>'161119ED4-08590ADA-4951-46C8-8DD1-17C0EB5C502F']);
//        $res = (new CreditCardController)->transactionAction('ccreturn', ['ssl_txn_id' => '161119ED4-08590ADA-4951-46C8-8DD1-17C0EB5C502F']);
//        $res = (new CreditCardController)->cardRefund( '161119ED4-08590ADA-4951-46C8-8DD1-17C0EB5C502F');
//        dd($res);
    }
}
