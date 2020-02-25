<?php

namespace App\Http\Controllers;

use App\Address;
use App\Payment;
use App\Customer;
use App\Giftcard;
use App\Helper\Price;
use App\Jobs\ProcessOrder;
use App\MasOrder;
use App\Order;
use App\StorePickup;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $order;
    private $order_data;
    private $user;
    private $store;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function all()
    {
        return response(Order::without([
            'items',
            'payments',
        ])->paginate());
    }

    public function getOne(Order $model)
    {
        $model = $model->load('customer.addresses');
        return response($model);
    }

    public function updateItems(Request $request)
    {
        $this->order_data = $request->validate([
            'order_id' => 'required|exists:orders,id',

            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            // items (products)
            'products' => 'required',
            'products.*.id' => 'nullable|numeric',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'required|string',
            'products.*.final_price' => 'required|numeric',
            'products.*.qty' => 'required|numeric',
            'products.*.discount_type' => 'nullable|string',
            'products.*.discount_amount' => 'nullable|numeric',
            'products.*.notes' => 'nullable|string',
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        // $this->order_data['user_id'] = $this->user->id;

        $addresses = $this->parseAddresses();

        $this->parseTax();

        $this->order = Order::findOrFail($this->order_data['order_id']);
        $this->order->fill($this->order_data);

        $this->parseProducts();

        $this->order->save();

        $this->updateOrderStatus(null, true);

        ProcessOrder::dispatchNow($this->order);

        return response(['notification' => [
            'msg' => ['Your changes in order\'s items saved successfully!'],
            'type' => 'success'
        ]]);
    }

    public function updateOptions(Request $request)
    {
        $this->order_data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required',
            'shipping_cost' => 'numeric|nullable',
            'method' => 'required|in:pickup,delivery',
            'notes' => 'string|nullable',

            // billing
            'billing_address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // delivery
            'delivery' => 'nullable|required_if:method,pickup,delivery|array',
            'delivery.date' => 'nullable|required_if:method,pickup,delivery|date',
            'delivery.time' => 'nullable|required_if:method,pickup,delivery|string',
            'delivery.occasion' => 'nullable|required_if:method,delivery|numeric',
            // delivery address (shipping address)
            'delivery.address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // store_pickup
            'delivery.store_pickup_id' => 'nullable|required_if:method,pickup|exists:store_pickups,id'
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->order_data['user_id'] = $this->user->id;

        $addresses = $this->parseAddresses();

        $this->order = Order::findOrFail($this->order_data['order_id']);
        $this->order->fill($this->order_data);

        $this->order->billing_address = $addresses['billing_address'] ?? null;

        $this->parseDelivery($addresses);

        $this->order->save();

        $this->updateOrderStatus(null, true);

        ProcessOrder::dispatchNow($this->order);

        return response(['notification' => [
            'msg' => ['Your changes in order\'s options saved successfully!'],
            'type' => 'success'
        ]]);
    }

    public function create(Request $request)
    {
        $this->order_data = $request->validate([
            'customer_id' => 'nullable|required_if:method,pickup,delivery|exists:customers,id',
            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            'shipping_cost' => 'numeric|nullable',
            'method' => 'required|in:retail,pickup,delivery',
            'notes' => 'string|nullable',
            // items (products)
            'products' => 'required',
            'products.*.id' => 'nullable|numeric',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'required|string',
            'products.*.final_price' => 'required|numeric',
            'products.*.qty' => 'required|numeric',
            'products.*.discount_type' => 'nullable|string',
            'products.*.discount_amount' => 'nullable|numeric',
            'products.*.notes' => 'nullable|string',
            // billing
            'billing_address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // delivery
            'delivery' => 'nullable|required_if:method,pickup,delivery|array',
            'delivery.date' => 'nullable|required_if:method,pickup,delivery|date',
            'delivery.time' => 'nullable|required_if:method,pickup,delivery|string',
            'delivery.occasion' => 'nullable|required_if:method,delivery|numeric',
            // delivery address (shipping address)
            'delivery.address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // store_pickup
            'delivery.store_pickup_id' => 'nullable|required_if:method,pickup|exists:store_pickups,id'
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->order_data['user_id'] = $this->user->id;
        $this->order_data['store_id'] = $this->store->id;

        $addresses = $this->parseAddresses();

        $this->parseTax();

        $this->order = new Order($this->order_data);

        $this->parseProducts();

        $this->order->billing_address = $addresses['billing_address'] ?? null;

        $this->parseDelivery($addresses);

        $this->order->save();

        ProcessOrder::dispatchNow($this->order);

        return response([
            'order_id' => $this->order->id,
            'order_status' => 'created',
            'order_total' => $this->order->total,
            'order_total_without_tax' => $this->order->total_without_tax,
            'order_total_tax' => $this->order->total_tax,
        ], 201);
    }

    private function parseDelivery(array $addresses)
    {
        $delivery = [];
        switch ($this->order_data['method']) {
            default:
            case 'retail':
                break;
            case 'pickup':
                $delivery['date'] = $this->order_data['delivery']['date'];
                $delivery['time'] = $this->order_data['delivery']['time'];
                $delivery['store_pickup'] = $addresses['store_pickup'];
                break;
            case 'delivery':
                $delivery['date'] = $this->order_data['delivery']['date'];
                $delivery['time'] = $this->order_data['delivery']['time'];
                $delivery['occasion'] = $this->order_data['delivery']['occasion'];
                $delivery['address'] = $addresses['delivery_address'];
                break;
        }

        $this->order->delivery = $delivery;
    }

    private function parseProducts()
    {
        $products = [];
        foreach ($this->order_data['products'] as $product) {
            array_push($products, $this->parseProduct($product));
        }

        $this->order->items = $products;
    }

    private function parseTax()
    {
        $has_tax = true;
        if (isset($this->order_data['customer_id'])) {
            $customer = Customer::findOrFail($this->order_data['customer_id']);
            $has_tax = $customer->no_tax ? false : true;
        }

        $this->order_data['subtotal'] = $this->setSubtotal($this->order_data);
        if ($has_tax) {
            $this->order_data['tax'] = $this->store->tax->percentage;
        } else {
            $this->order_data['tax'] = 0;
        }
    }

    private function parseAddresses()
    {
        $response = [];
        if (isset($this->order_data['billing_address_id'])) {
            $response['billing_address'] =  Address::findOrFail($this->order_data['billing_address_id']);
            unset($this->order_data['billing_address_id']);
        }

        if (isset($this->order_data['delivery']) && isset($this->order_data['delivery']['address_id'])) {
            $response['delivery_address'] =  Address::findOrFail($this->order_data['delivery']['address_id']);
            unset($this->order_data['delivery']['address_id']);
        }

        if (isset($this->order_data['delivery']) && isset($this->order_data['delivery']['store_pickup_id'])) {
            $response['store_pickup'] =  StorePickup::findOrFail($this->order_data['delivery']['store_pickup_id']);
            unset($this->order_data['delivery']['store_pickup_id']);
        }

        return $response;
    }

    private function setSubtotal()
    {
        $subtotal = 0;
        foreach ($this->order_data['products'] as $product) {
            $total = $product['final_price'] * $product['qty'];

            if (isset($product['discount_type']) && isset($product['discount_amount'])) {
                $total = Price::calculateDiscount($total, $product['discount_type'], $product['discount_amount']);
            }

            $subtotal += $total;
        }
        if (isset($this->order_data['discount_type']) && isset($this->order_data['discount_amount'])) {
            $subtotal = Price::calculateDiscount($subtotal, $this->order_data['discount_type'], $this->order_data['discount_amount']);
        }

        return $subtotal;
    }

    private function parseProduct($product)
    {
        $product['price'] = $product['final_price'];

        unset($product['stores']);
        unset($product['stock_id']);
        unset($product['categories']);
        unset($product['created_at']);
        unset($product['updated_at']);
        unset($product['description']);
        unset($product['laravel_stock']);
        unset($product['magento_stock']);
        unset($product['plantcare_pdf']);

        if (
            !array_key_exists('discount_type', $product) ||
            !array_key_exists('discount_amount', $product)
        ) {
            $product['discount_type'] = $product['discount_amount'] = null;
        } else if ($product['discount_type'] && $product['discount_amount']) {
            $product['final_price'] = Price::calculateDiscount(
                $product['price'],
                $product['discount_type'],
                $product['discount_amount']
            );
        }

        return $product;
    }

    public function rollbackOrder(Order $model)
    {
        foreach ($model->items as $product) {
            if (isset($product['sku'])) {
                $code = explode('-', $product['sku']);
                if (count($code) > 1 && $code[0] === 'giftCard') {
                    $giftCard = Giftcard::whereCode($code[1])->first();

                    if (!$giftCard) {
                        return response(['errors' => ['Gift Card' => "Gift card with code: $code[1] not found"]], 422);
                    }

                    $giftCard->amount -= $product->final_price;

                    if ($giftCard->amount >= 0) {
                        $giftCard->save();
                    } else {
                        return response(['errors' =>  ['Order cancellation' => "Gift card with code: {$giftCard->code} has insufficient balance to cancel the order with id: $model->id<br>No changes where made"]], 422);
                    }
                }
            }
        }

        $results = $this->rollbackPayments($model);

        if (!$results || (is_array($results) && array_key_exists('errors', $results))) {
            return response(['errors' => $results['errors']], 500);
        }


        $model->status = 'canceled';
        $model->save();

        return response([
            'notification' => [
                'msg' => ["Order #{$model->id} successfully canceled!"],
                'type' => 'success'
            ],
            'data' => $model
        ]);
    }

    public function search(Request $request)
    {
        $this->order_data = $request->validate([
            'keyword' => 'required|string'
        ]);

        return Order::without([
            'items',
            'payments',
            'store_pickup'
        ])
            ->orWhere('status',  'LIKE', "%{$this->order_data['keyword']}%")
            ->orWhere('occasion', 'LIKE', "%{$this->order_data['keyword']}%")
            ->orWhere('location', 'LIKE', "%{$this->order_data['keyword']}%")
            ->paginate();
    }

    public function updateOrderStatus(Payment $payment = null, bool $refund = false)
    {
        $remaining = Price::numberPrecision($this->order->total - $this->order->total_paid);
        $change = Price::numberPrecision($this->order->total_paid - $this->order->total);

        if ($change < 0) {
            $change = 0;
        }

        if ($remaining < 0) {
            $remaining = 0;
        }

        if ($remaining > 0) {
            if ($this->order->status !== 'pending_payment') {
                $this->order->change = 0;
                $this->order->status = 'pending_payment';
            }
        } else {
            if (!$refund) {
                $payment->amount = Price::numberPrecision($payment->amount - $change);
                $payment->save();

                if ($this->order->status !== 'paid') {
                    $this->order->change = $change;
                    $this->order->status = 'paid';
                    MasOrder::create([
                        'order_id' => $this->order->id,
                        'status' => 'queued'
                    ]);
                }
            } else {
                if (empty($payment)) {
                    $this->order->status = 'paid';
                }
                $this->order->change = $change;
                $this->order->save();
            }
        }

        $this->order->save();

        $this->order = $this->order->refresh();
        return [
            'remaining' => $remaining,
            'change' =>  $change,
            'order_status' => $this->order->status
        ];
    }

    public function rollbackPayments(Order $order)
    {
        $paymentController = new PaymentController();
        $results = [];

        foreach ($order->payments as $payment) {
            if ($payment->status === 'approved' && !$payment->refunded) {
                $result = $paymentController->refundPayment($payment, false);

                if (is_array($result) && array_key_exists('errors', $result)) {
                    if (!array_key_exists('errors', $results)) {
                        $results['errors'] = [];
                    }
                    array_push($results['errors'], $result['errors']);
                }
            }
        }

        return $results ? $results : true;
    }
}
