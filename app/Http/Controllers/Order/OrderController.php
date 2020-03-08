<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use App\Giftcard;
use App\Jobs\ProcessOrder;
use App\Order;
use App\Status;
use App\StorePickup;
use Illuminate\Database\Eloquent\Builder;
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
        return response()->json($model->load(['customer.addresses', 'payments']), 200, [], JSON_NUMERIC_CHECK);
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
            'products.*.price' => 'required|array',
            'products.*.price.amount' => 'required|numeric|integer',
            'products.*.qty' => 'required|numeric',
            'products.*.discount' => 'required|array',
            'products.*.discount.amount' => 'nullable|numeric|integer',
            'products.*.discount.type' => 'nullable|string|in:none,flat,percentage',
            'products.*.notes' => 'nullable|string',
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->parseStoreData();
        $this->parseProducts();

        $this->order = Order::findOrFail($this->order_data['order_id']);

        $this->order->fill($this->order_data);

        $orderStatusController = new OrderStatusController($this->order);
        $orderStatusController->updateOrderStatus(null, true);

        // ProcessOrder::dispatchNow($this->order);

        return response(['notification' => [
            'msg' => ['Your changes in order items saved successfully!'],
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
        $this->parseAddresses();

        $this->order = Order::findOrFail($this->order_data['order_id']);
        $this->order->fill($this->order_data);

        $orderStatusController = new OrderStatusController($this->order);
        $orderStatusController->updateOrderStatus(null, true);

        // ProcessOrder::dispatchNow($this->order);

        return response(['notification' => [
            'msg' => ['Your changes in order options saved successfully!'],
            'type' => 'success'
        ]]);
    }

    public function create(Request $request)
    {
        $this->order_data = $request->validate([
            'method' => 'required|in:retail,pickup,delivery',
            // items (products)
            'products' => 'required',
            'products.*.id' => 'nullable|numeric',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'required|string',
            'products.*.price' => 'required|array',
            'products.*.price.amount' => 'required|numeric|integer',
            'products.*.qty' => 'required|numeric',
            'products.*.discount' => 'required|array',
            'products.*.discount.amount' => 'nullable|numeric|integer',
            'products.*.discount.type' => 'nullable|string|in:none,flat,percentage',
            'products.*.notes' => 'nullable|string',
            'notes' => 'string|nullable',
            // customer
            'customer_id' => 'nullable|required_if:method,pickup,delivery|exists:customers,id',
            'billing_address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // delivery
            'delivery' => 'nullable|required_if:method,pickup,delivery|array',
            'delivery.date' => 'nullable|required_if:method,pickup,delivery|date',
            'delivery.time' => 'nullable|required_if:method,pickup,delivery|string',
            'delivery.occasion' => 'nullable|required_if:method,delivery|numeric',
            // delivery address
            'delivery.address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // store_pickup
            'delivery.store_pickup_id' => 'nullable|required_if:method,pickup|exists:store_pickups,id',
            'delivery_fees_price' => 'nullable|array',
            'delivery_fees_price.amount' => 'nullable|integer',
            'delivery_fees_price.currency' => 'nullable|string|size:3',
            // discount
            'discount' => 'required|array',
            'discount.amount' => 'nullable|numeric|integer',
            'discount.type' => 'nullable|string|in:none,flat,percentage',
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->order_data['user_id'] = $this->user->id;
        $this->order_data['store_id'] = $this->store->id;

        $this->parseAddresses();
        $this->parseStoreData();
        $this->parseProducts();

        $this->order = Order::createWithoutEvents($this->order_data);
        $submittedStatusId = Status::where('value', 'submitted')->firstOrFail('id');
        $this->order->statuses()->attach($submittedStatusId, ['user_id' => $this->user->id]);
        // ProcessOrder::dispatchNow($this->order);

        return response([
            'id' => $this->order->id,
            'status' => $this->order->status,
            'total_price' => $this->order->total_price,
            'tax_price' => $this->order->tax_price,
        ], 201);
    }

    private function parseDelivery(array $addresses)
    {
        $delivery = [];
        switch ($this->order_data['method']) {
            default:
            case 'retail':
                unset($this->order_data['delivery']);
                return;
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

        $this->order_data['delivery'] = $delivery;
    }

    private function parseProducts()
    {
        $items = [];
        foreach ($this->order_data['products'] as $product) {
            array_push($items, $this->parseProduct($product));
        }

        unset($this->order_data['products']);
        $this->order_data['items'] = $items;
    }

    private function parseStoreData()
    {
        $this->order_data['currency'] = $this->store->default_currency;

        $has_tax = true;
        if (isset($this->order_data['customer_id'])) {
            $customer = Customer::findOrFail($this->order_data['customer_id']);
            $has_tax = $customer->no_tax ? false : true;
        }

        if ($has_tax) {
            $this->order_data['tax_percentage'] = $this->store->tax->percentage;
        } else {
            $this->order_data['tax_percentage'] = 0;
        }
    }

    private function parseAddresses()
    {
        $addresses = [];
        if (isset($this->order_data['billing_address_id'])) {
            $this->order_data['billing_address'] = Address::findOrFail($this->order_data['billing_address_id']);
            unset($this->order_data['billing_address_id']);
        }

        if (isset($this->order_data['delivery']) && isset($this->order_data['delivery']['address_id'])) {
            $addresses['delivery_address'] =  Address::findOrFail($this->order_data['delivery']['address_id']);
            unset($this->order_data['delivery']['address_id']);
        }

        if (isset($this->order_data['delivery']) && isset($this->order_data['delivery']['store_pickup_id'])) {
            $addresses['store_pickup'] =  StorePickup::findOrFail($this->order_data['delivery']['store_pickup_id']);
            unset($this->order_data['delivery']['store_pickup_id']);
        }

        $this->parseDelivery($addresses);
    }

    private function parseProduct($product)
    {
        // remove useless data
        unset($product['stores']);
        unset($product['stock_id']);
        unset($product['categories']);
        unset($product['created_at']);
        unset($product['updated_at']);
        unset($product['description']);
        unset($product['laravel_stock']);
        unset($product['magento_stock']);
        unset($product['plantcare_pdf']);

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

    public function search(Request $request)
    {
        $this->order_data = $request->validate([
            'keyword' => 'nullable|required_without:filters|string',

            'filters' => 'nullable|array',
            // checkboxes
            'filters.cb_timestamps' => 'nullable|boolean',
            'filters.cb_statuses' => 'nullable|boolean',
            'filters.cb_customer' => 'nullable|boolean',
            // filters
            'filters.timestamp_from' => 'nullable|before_or_equal:today',
            'filters.timestamp_to' => 'nullable|before_or_equal:today',
            'filters.statuses.*.id' => 'nullable|exists:statuses,id',
            'filters.customer_id' => 'nullable|exists:customers,id',
        ]);

        $query = Order::without([
            'items',
            'payments',
            'store_pickup'
        ]);

        if (isset($this->order_data['keyword'])) {
            $query = $query
                ->orWhere('location', 'LIKE', "%{$this->order_data['keyword']}%");
        }

        if (isset($this->order_data['filters'])) {
            $query = $this->applyFilters($query);
        }

        return response($query->paginate());
    }

    private function applyFilters($query)
    {
        if (isset($this->order_data['filters']['cb_timestamps']) && $this->order_data['filters']['cb_timestamps']) {
            if (isset($this->order_data['filters']['timestamp_from']) && !isset($this->order_data['filters']['timestamp_to'])) {
                $query =
                    $query->where('created_at', '>=', $this->order_data['filters']['timestamp_from']);
            } else if (isset($this->order_data['filters']['timestamp_to'])) {
                $query = $query->where('created_at', '<=', [$this->order_data['filters']['timestamp_to']]);
            } else if (isset($this->order_data['filters']['timestamp_from']) && isset($this->order_data['filters']['timestamp_to'])) {
                $query =
                    $query->whereBetween(
                        'created_at',
                        [
                            $this->order_data['filters']['timestamp_from'],
                            $this->order_data['filters']['timestamp_to']
                        ]
                    );
            }
        }

        if (isset($this->order_data['filters']['cb_customer']) && $this->order_data['filters']['cb_customer']  && isset($this->order_data['filters']['customer_id'])) {
            $query = $query->where('customer_id', $this->order_data['filters']['customer_id']);
        }

        if (isset($this->order_data['filters']['cb_statuses']) && $this->order_data['filters']['cb_statuses']  && isset($this->order_data['filters']['statuses'])) {
            $statuses = $this->order_data['filters']['statuses'];
            $query = $query->with('statuses')->whereHas('statuses', function (Builder $query) use ($statuses) {
                $query->latest()->whereIn('status_id', $statuses);
            });
        }

        return $query;
    }
}
