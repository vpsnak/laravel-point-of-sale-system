<?php

namespace App\Console\Commands;

use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\Magento\Script\GetCustomers;
use App\Http\Controllers\OrderMASController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

//        $this->transcase(
//            '1011',
//            'ccvoid',
//            '211119AD3-2E0FF16C-C9EF-4607-BA8E-CCA55350BF3A'
//        );
//
//        $this->transcase(
//            '1012',
//            'txnquery',
//            '211119ED3-0D3ED3C9-0545-4FB6-8E0A-B1887E212A71'
//        );
//
//        $this->cardcase(
//            '1013',
//            'ccsale',
//            7,
//            'visa',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->transcase(
//            '1014',
//            'txnquery',
//            '211119AD4-38A03906-4CD6-4F7D-90D2-68828C8C73DF'
//        );
//
//        $this->cardcase(
//            '1016',
//            'ccauthonly',
//            911,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1017',
//            'ccauthonly',
//            77,
//            'amex',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1018',
//            'ccauthonly',
//            213,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1019',
//            'ccauthonly',
//            351,
//            'mc',
//            0,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1020',
//            'ccauthonly',
//            609,
//            'visa',
//            2,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1021',
//            'ccauthonly',
//            381,
//            'amex',
//            9,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1022',
//            'ccauthonly',
//            433,
//            'visa',
//            1,
//            true,
//            true,
//            true
//        );
//
//        $this->cardcase(
//            '1023',
//            'ccauthonly',
//            894,
//            'amex',
//            1,
//            true,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1024',
//            'ccauthonly',
//            513,
//            'disc',
//            0,
//            true,
//            true,
//            true
//        );
//
//        $this->cardcase(
//            '1025',
//            'ccsale',
//            165,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1026',
//            'ccsale',
//            475,
//            'visa',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1027',
//            'ccsale',
//            1109,
//            'mc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->transcase(
//            '1029',
//            'txnquery',
//            '211119AD3-9431F188-090D-4959-909B-A85EC76499CD'
//        );
//
//        $this->cardcase(
//            '1030',
//            'ccsale',
//            20,
//            'visa',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1039',
//            'ccsale',
//            213,
//            'amex',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1040',
//            'ccsale',
//            351,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->transcase(
//            '1041',
//            'txnquery',
//            '211119ED3-D3C31AE6-1B5E-43F1-B10B-5FD5AE64FBBD'
//        );
//
//        $this->cardcase(
//            '1043',
//            'cccredit',
//            195,
//            'amex',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1044',
//            'ccsale',
//            137,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1045',
//            'ccsale',
//            165,
//            'mc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1046',
//            'ccsale',
//            475,
//            'visa',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1047',
//            'ccsale',
//            1109,
//            'amex',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->transcase(
//            '1048',
//           'ccvoid',
//           '211119ED3-1364D7DB-AD4E-408B-ACFB-0453133AC46A'
//        );
//
//        $this->transcase(
//            '1049',
//           'ccvoid',
//           '211119ED3-3B314ED8-DC43-453F-8B2C-B5B3E5D3DD82'
//        );
//
//        $this->transcase(
//            '1050',
//           'ccvoid',
//           '211119ED3-9F9D295F-F09F-411E-A255-EF0940081BD2'
//        );
//
//        $this->transcase(
//            '1051',
//           'ccvoid',
//           '211119ED4-4D95A871-F36A-414D-9FAB-CC5E169495CE'
//        );
//
//        $this->cardcase(
//            '1052',
//            'ccsale',
//            121.80,
//            'disc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1053',
//            'ccsale',
//            121.80,
//            'mc',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1054',
//            'ccsale',
//            121.80,
//            'visa',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->cardcase(
//            '1055',
//            'ccsale',
//            121.80,
//            'amex',
//            1,
//            false,
//            false,
//            false
//        );
//
//        $this->transcase(
//            '1056',
//           'ccreturn',
//           '211119ED4-7638FB45-E39D-440B-ABD7-FF76CE01E3AC'
//        );

        $this->transcase(
            '1057',
            'ccreturn',
            '211119ED3-B01CF4D0-72E2-48A2-B51F-832B8FE8DEA0'
        );
//
//        $this->transcase(
//            '1058',
//           'ccreturn',
//           '211119ED4-956C4CBD-15A3-4468-8D17-1C500091177E'
//        );
//
//        $this->transcase(
//            '1059',
//           'ccreturn',
//           '211119ED4-4B680811-8064-4999-BD33-AA1F4E560975'
//        );

    }

    public function cardcase(
        $testcase,
        $type,
        $amount,
        $card,
        $indicator,
        $invoice,
        $address,
        $zip
    ) {
        $api = new CreditCardController();
        Log::channel('stock')->debug("Case: API $testcase");
        $api->init(
            $testcase,
            $amount,
            $card,
            $indicator,
            $invoice,
            $address,
            $zip
        );

        $res = $api->creditCardAction(
            $type,
            $api->ssl_card_number,
            '1222',
            $api->ssl_cvv2cvc2,
            'Test Case',
            $api->ssl_amount
        );
        $api->saveToApiLog($res, array_key_exists('success', $res) ? 'APPROVED' : 'DECLINED');
    }

    public function transcase(
        $testcase,
        $type,
        $transaction_id,
        $amount = null
    ) {
        $api = new CreditCardController();
        Log::channel('stock')->debug("Case: API $testcase");
        $api->init(
            $testcase,
            $amount = null
        );

        $res = $api->transactionAction(
            $type,
            [
                'ssl_txn_id' => $transaction_id,
                'ssl_amount' => $amount
            ]
        );
        $api->saveToApiLog($res, array_key_exists('success', $res) ? 'APPROVED' : 'DECLINED');
    }
}
