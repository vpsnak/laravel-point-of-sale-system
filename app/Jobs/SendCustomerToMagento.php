<?php

namespace App\Jobs;

use App\Customer;
use App\Http\Controllers\Magento\Script\CustomerSync;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCustomerToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * Create a new job instance.
     *
     * SendCustomerToMagento constructor.
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('trying ' . $this->customer->email);
        CustomerSync::importToMagento($this->customer);
    }
}
