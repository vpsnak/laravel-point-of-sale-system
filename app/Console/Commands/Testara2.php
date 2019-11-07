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
        $card = [
            'type' => 'visa',
            'number' => '4000000000000002',
            'exp_date' => '1219',
            'cvc' => '123',
            'name' => 'Test Name',
        ];
        $response = (new CreditCardController)->creditCardAction(
            'ccsale',
            $card['number'],
            $card['exp_date'],
            $card['cvc'],
            $card['name'],
            25
        );
        var_dump($response);
        $response = (new CreditCardController)->transactionAction('ccvoid', [
            'ssl_txn_id' => $response['id']
        ]);
        var_dump($response);
    }
}
