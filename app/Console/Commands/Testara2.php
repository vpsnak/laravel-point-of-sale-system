<?php

namespace App\Console\Commands;

use App\Http\Controllers\CreditCardController;
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
        // $response = (new CreditCardController)->transactionAction('ccvoid', [
        //     "ssl_merchant_id" => '009710',
        //     "ssl_user_id" => 'convergeapi',
        //     "ssl_pin" => 'H3192LKKOIBN3GIZ9FGXX01MYWGIYEJ2UJ8PW4PCHXRSNBZVFMF1PWNG0HR003MI',
        //     'ssl_txn_id' => '200120ED4-2D1BEFB4-8B6F-4D86-92EE-B856560A82C8'
        // ]);
    }
}
