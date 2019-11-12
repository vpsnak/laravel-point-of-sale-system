<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                return ['errors' => 'Invalid API action'];
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
                'errors' => $response['errorName'] . ' - ' . $response['errorMessage']
            ];
        }
        return [
            'success' => $response['ssl_result_message'],
            'id' => $response['ssl_txn_id']
        ];
    }

    public function cardRefund($card_number, $exp_date, $cvc, $card_holder, $amount)
    {
        return $this->creditCardAction('cccredit', $card_number, $exp_date, $cvc, $card_holder, $amount);
    }

    public function transactionAction($type, $params)
    {
        $payload = [];
        if (array_key_exists('ssl_txn_id', $params)) {
            $payload['ssl_txn_id'] = $params['ssl_txn_id'];
        }
        if (array_key_exists('ssl_amount', $params)) {
            $payload['ssl_amount'] = $params['ssl_amount'];
        }

        switch ($type) {
            case 'ccreturn':
            case 'ccvoid':
            case 'cccomplete':
            case 'ccdelete':
                $response = ElavonApiPaymentController::doTransaction($type, $payload);
                return $this->prepareResponse($response);
                break;
            default:
                return ['errors' => 'Invalid API action'];
                break;
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
        array_key_exists('cvc2_indicator',
            $validatedData) ? $this->cvc2_indicator = $validatedData['cvc2_indicator'] : $this->cvc2_indicator = 1;
        array_key_exists('invoiceNumber',
            $validatedData) ? $this->invoice_number = 'INV101' : $this->invoice_number = null;
        array_key_exists('address', $validatedData) ? $this->address = '10025' : $this->address = null;
        array_key_exists('zipcode', $validatedData) ? $this->zipcode = '10025' : $this->zipcode = null;
    }
}
