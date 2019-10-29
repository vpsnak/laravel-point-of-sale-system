<?php

namespace App\Observers;

use App\Address;

class CustomerAddressObserver
{
    /**
     * Handle the address "created" event.
     *
     * @param Address $address
     * @return void
     */
    public function created(Address $address)
    {
        $this->correctDefaultAddressesIfNone($address);
    }

    // @TODO Evaluate if needed to run here or in general
    private function correctDefaultAddressesIfNone(Address $address)
    {
//        foreach ($address->customers as $customer) {
//            $hasBilling = $customer->addresses->where('billing', '=', 1)->count();
//            $hasShipping = $customer->addresses->where('shipping', '=', 1)->count();
//            if ($hasBilling != 1) {
//                $customer->addresses->first()->update(['billing' => 1]);
//            }
//            if ($hasShipping != 1) {
//                $customer->addresses->first()->update(['shipping' => 1]);
//            }
//        }
    }

    /**
     * Handle the address "updated" event.
     *
     * @param Address $address
     * @return void
     */
    public function updated(Address $address)
    {
        $this->correctDefaultAddressesIfNone($address);
    }
}
