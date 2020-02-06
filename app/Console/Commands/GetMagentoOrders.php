<?php

namespace App\Console\Commands;

use App\Jobs\FetchMagentoOrder;
use App\Magento\MagentoClient;
use Illuminate\Console\Command;

class GetMagentoOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:get:orders';

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
        $client = new MagentoClient();

        $page = 339;
        $per_page = 100;
        $ordersRetrieved = $per_page;
        $previous_orders = [];
        $current_orders = [];
        while ($ordersRetrieved == $per_page) {
            $previous_orders = $current_orders;
            $current_order = [];
            $orders = $client->get("orders?limit=$per_page&page=$page", []);
            $page++;
            if (empty($orders)) {
                break;
            }

            foreach ($orders as $order) {
                $current_orders[] = $order->entity_id;
            }
            if (empty(array_diff($current_orders, $previous_orders))) {
                var_dump('end reached');
                break;
            }

            foreach ($orders as $order) {
                FetchMagentoOrder::dispatch($order);
            }
        }
    }
}
