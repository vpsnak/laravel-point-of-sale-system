<?php

namespace App\Console\Commands;

use App\PostalCode;
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
        $postalcode = PostalCode::skip(2)->first();
        var_dump(
            $postalcode->postalcode
        );
        var_dump(
            $postalcode->grid->group->group_name
        );
        foreach ($postalcode->grid->group->timeslotGrid as $grid) {
            var_dump(
                $grid->timeslot->title
            );
        }
    }
}
