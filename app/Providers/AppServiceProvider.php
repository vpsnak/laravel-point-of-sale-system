<?php

namespace App\Providers;

use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\PaymentObserver;
use App\Order;
use App\OrderProduct;
use App\Payment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Order::observe(OrderObserver::class);
        OrderProduct::observe(OrderProductObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
