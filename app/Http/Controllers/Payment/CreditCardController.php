<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class CreditCardController extends Controller
{
    protected $cvc2_indicator;
    protected $invoice_number;
    protected $address;
    protected $zipcode;

    public function cardPayment($card_number, $exp_date, $cvc, $card_holder, $amount)
    {
        return $this->creditCardAction('ccsale', $card_number, $exp_date, $cvc, $card_holder, $amount);
    }

    public function creditCardAction(
        $type,
        $card_number,
        $exp_date,
        $cvc,
        $card_holder,
        $amount
    ) {
        $payload = $this->preparePaymentPayload(
            $card_number,
            $exp_date,
            $cvc,
            $card_holder,
            $amount
        );

        switch ($type) {
            case 'ccsale':
            case 'ccauthonly':
            case 'ccbalinquiry':
            case 'cccredit': // @TODO check if it works
                $response = ElavonApiPaymentController::doTransaction($type, $payload);
                return $this->prepareResponse($response);
                break;
            default:
                return ['errors' => ['Invalid API action']];
                break;
        }
    }

    private function preparePaymentPayload(
        $card_number,
        $exp_date,
        $cvc,
        $card_holder,
        $amount
    ) {
        /**
         * ssl_card_present
         * Y - Card present
         * N - Card not present
         *
         * ssl_pos_mode - Indicates how the POS device captures card data.
         * 01 - Manual entry only
         * 02 - Magnetic swipe-capable
         * 03 - Proximity or contactless-capable
         * 04 - EMV chip-capable (ICC) - Contact only with magnetic stripe
         * 05 - EMV chip-capable (ICC) - Dual interface with magnetic stripe
         *
         * ssl_entry_mode - Indicates how the track data was captured.
         * 01 - Key entered - Card not present
         * 02 - Key entered - Card present
         * 03 - Swiped (default)
         * 04 - Proximity/Contactless
         *
         * ssl_salestax_indicator - Whether tax is included on a transaction.
         * Y = Sales Tax Included
         * N or Zero = Tax Exempted Sale (card acceptor has the ability to provide tax amount, but goods/services were not taxable)
         */
        $payload = [
            'ssl_card_present' => 'N',
            'ssl_pos_mode' => '01',
            'ssl_entry_mode' => '01',
            'ssl_card_number' => $card_number,
            'ssl_exp_date' => $exp_date,
            'ssl_salestax_indicator' => 'Y', // @TODO tax exempt
            'ssl_amount' => $amount,
            'ssl_cvv2cvc2_indicator' => $this->cvc2_indicator,
            'ssl_cvv2cvc2' => $cvc,
            'ssl_first_name' => $card_holder,
        ];

        if (!empty($this->address) && !empty($this->zipcode)) {
            $payload['ssl_avs_address'] = $this->address;
            $payload['ssl_avs_zip'] = $this->zipcode;
        }

        return $payload;
    }

    private function prepareResponse($response)
    {
        if (array_key_exists('errorCode', $response)) {
            return [
                'errors' => [$response['errorName'] . ' - ' . $response['errorMessage']],
                'response' => $response
            ];
        }
        if (array_key_exists('ssl_result_message', $response)) {
            if ($response['ssl_result_message'] == 'APPROVAL') {
                return [
                    'success' => [$response['ssl_result_message']],
                    'id' => $response['ssl_txn_id'],
                    'response' => $response
                ];
            }
            return [
                'errors' => [
                    'Unknown Error',
                    $response['ssl_result_message']
                ],
                'id' => $response['ssl_txn_id'],
                'response' => $response
            ];
        }
        return [
            'errors' => ['Undocumented Error'],
            'response' => $response
        ];
    }

    public function cardRefund($transaction_id)
    {
        $payment = Payment::where('code', $transaction_id)->firstOrFail();
        $store = $payment->cash_register->store;

        $data = [
            'ssl_merchant_id' => ($store->company->bankAccountApi()->account)['merchant_id'],
            'ssl_user_id' => ($store->company->bankAccountApi()->account)['user_id'],
            'ssl_pin' => ($store->company->bankAccountApi()->account)['pin'],
            'ssl_txn_id' => $transaction_id
        ];

        $response = ElavonApiPaymentController::doTransaction('txnquery', $data);
        $parsedResponse = $this->prepareResponse($response);
        if (array_key_exists('errors', $parsedResponse)) {
            return ['errors' => ['Refund' => 'Can not refund!']];
        }
        if (array_key_exists('ssl_is_voidable', $response)) {
            if ($response['ssl_is_voidable'] == 'TRUE') {
                return $this->transactionAction('ccvoid', ['ssl_txn_id' => $transaction_id]);
            } else {
                return $this->transactionAction('ccreturn', ['ssl_txn_id' => $transaction_id]);
            }
        }
        return ['errors' => ['Refund' => 'Cannot refund!']];
    }

    public function transactionAction($type, $params)
    {
        switch ($type) {
            case 'ccreturn':
            case 'ccvoid':
            case 'cccomplete':
            case 'ccdelete':
            case 'txnquery':
                $response = ElavonApiPaymentController::doTransaction($type, $params);
                return $this->prepareResponse($response);
            default:
                return ['errors' => ['Invalid API action']];
        }
    }

    /**
     *
     * cvc2 - Card Validation Code Indicator
     * 0 - Bypassed
     * 1 - Present
     * 2 - Illegible
     * 9 - Not Present
     *
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'cvc2_indicator' => 'nullable|boolean',
            'invoiceNumber' => 'nullable|string',
            'address' => 'nullable|string',
            'zipcode' => 'nullable|string',
        ]);

        array_key_exists('amount', $validatedData) ? $this->amount = $validatedData['amount'] : null;
        array_key_exists(
            'cvc2_indicator',
            $validatedData
        ) ? $this->cvc2_indicator = $validatedData['cvc2_indicator'] : $this->cvc2_indicator = 1;
        array_key_exists(
            'invoiceNumber',
            $validatedData
        ) ? $this->invoice_number = 'INV101' : $this->invoice_number = null;
        array_key_exists('address', $validatedData) ? $this->address = '10025' : $this->address = null;
        array_key_exists('zipcode', $validatedData) ? $this->zipcode = '10025' : $this->zipcode = null;
    }
}
