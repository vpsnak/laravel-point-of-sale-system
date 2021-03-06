<?php

namespace App\Http\Controllers;

use App\Address;
use App\Customer;
use App\Helper\Price;
use App\Jobs\ProcessOrder;
use App\Order;
use App\Status;
use App\StorePickup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

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
        return response(Order::orderBy('created_at', 'desc')->paginate(10));
    }

    public function getOne(Order $model)
    {
        return response($model->load(['customer.addresses']));
    }

    public function updateItems(Request $request)
    {
        $this->order_data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            // order discount
            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            // items (products)
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'nullable|required_without:products.*.code|string',
            'products.*.code' => 'nullable|required_without:products.*.sku|string',
            'products.*.price' => 'required|array',
            'products.*.price.amount' => 'required|numeric|integer',
            'products.*.qty' => 'required|numeric|min:1|max:500',
            'products.*.discount' => 'nullable|array',
            'products.*.discount.amount' => 'nullable|numeric|integer',
            'products.*.discount.type' => 'nullable|string|in:none,flat,percentage',
            'products.*.notes' => 'nullable|string',
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->parseProducts();

        $this->order = Order::findOrFail($this->order_data['order_id']);

        $this->order->update($this->order_data);

        $orderStatusController = new OrderStatusController($this->order);
        $orderStatusController->updateOrderStatus(true);

        ProcessOrder::dispatchNow($this->order);

        return response(['notification' => [
            'msg' => ['Your changes in order items saved successfully!'],
            'type' => 'success'
        ]]);
    }

    public function updateOptions(Request $request)
    {
        $this->order_data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:pickup,delivery',

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
        ]);

        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->parseAddresses();
        $this->parseStoreData();

        $this->order = Order::findOrFail($this->order_data['order_id']);
        $this->order->update($this->order_data);

        $orderStatusController = new OrderStatusController($this->order);
        $orderStatusController->updateOrderStatus(true);

        ProcessOrder::dispatchNow($this->order);

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
            'products.*.id' => 'required',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'nullable|required_without:products.*.code|string',
            'products.*.code' => 'nullable|required_without:products.*.sku|string',
            'products.*.type' => 'required|string|in:product,giftcard,custom',
            'products.*.price' => 'required|array',
            'products.*.price.amount' => 'required|numeric|integer',
            'products.*.qty' => 'required|numeric|min:1|max:500',
            'products.*.discount' => 'nullable|array',
            'products.*.discount.amount' => 'nullable|numeric|integer',
            'products.*.discount.type' => 'nullable|string|in:none,flat,percentage',
            'products.*.notes' => 'nullable|string',
            'notes' => 'string|nullable',
            // discount
            'discount' => 'nullable|array',
            'discount.amount' => 'nullable|numeric|integer',
            'discount.type' => 'nullable|string|in:none,flat,percentage',
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
        ]);
        $this->user = auth()->user();
        $this->store = $this->user->open_register->cash_register->store;

        $this->order_data['created_by_id'] = $this->user->id;
        $this->order_data['store_id'] = $this->store->id;

        $this->parseAddresses();
        $this->parseStoreData();
        $this->parseProducts();
        $this->order = Order::create($this->order_data);

        $submittedStatusId = Status::where('value', 'submitted')->firstOrFail('id');
        $this->order->statuses()->attach($submittedStatusId, ['processed_by_id' => $this->user->id]);

        ProcessOrder::dispatchNow($this->order);

        return
            response([
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

    public function cancelOrder(Order $model)
    {
        $results = $this->rollbackPayments($model);

        if ((is_array($results) && array_key_exists('errors', $results))) {
            return response(['errors' => $results['errors']], 500);
        }

        $canceledStatusId = Status::where('value', 'canceled')->firstOrFail('id');
        $model->statuses()->attach($canceledStatusId, ['processed_by_id' => auth()->user()->id]);

        return response([
            'notification' => [
                'msg' => ["Order #{$model->id} successfully canceled"],
                'type' => 'success'
            ],
            'data' => $model
        ]);
    }

    public function rollbackPayments(Order $order)
    {
        $transactionController = new TransactionController();
        $results = [];

        foreach ($order->transactions as $transaction) {
            if ($transaction->status === 'approved' && !$transaction->refund_id) {
                $result = $transactionController->rollbackPayment($transaction->payment, false, null, false);

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
            'store_pickup'
        ]);

        if (isset($this->order_data['filters'])) {
            $query = $this->applyFilters($query);
        }

        return response($query->paginate(10));
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

        if (
            isset($this->order_data['filters']['cb_statuses'])
            && $this->order_data['filters']['cb_statuses']
            && isset($this->order_data['filters']['statuses'])
        ) {
            $statuses = $this->order_data['filters']['statuses'];
            $query = $query
                ->whereHas('lastStatus', function (Builder $query) use ($statuses) {
                    $query
                        ->whereIn('status_id', $statuses);
                });
        }

        return $query;
    }

    public function printCustomerOrder(Order $order)
    {
        $order = $order->load(['store']);

        $barcode = new BarcodeGenerator();
        if ($order->id) {
            $barcode->setText(strval($order->id));
        }
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(0);
        $code = $barcode->generate();

        $customer_billing_address = null;
        if ($order->customer) {
            $customer_billing_address = $order->customer->getDefaultBilling();
        }

        return view('customers_order')->with([
            'order' => $order,
            'store' => $order->store,
            'customer_billing_address' => $customer_billing_address,
            'code' => $code,
            'moneyFormatter' => Price::newFormatter()
        ]);
    }

    public function printOrder(Order $order)
    {
        $order = $order->load(['store']);

        $barcode = new BarcodeGenerator();
        if ($order->id) {
            $barcode->setText(strval($order->id));
        }
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(0);
        $code = $barcode->generate();

        $customer_billing_address = null;
        if ($order->customer) {
            $customer_billing_address = $order->customer->getDefaultBilling();
        }

        return view('order')->with([
            'order' => $order,
            'store' => $order->store,
            'customer_billing_address' => $customer_billing_address,
            'code' => $code,
            'moneyFormatter' => Price::newFormatter()
        ]);
    }
}
