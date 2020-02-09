<?php

namespace App\Listeners;

use App\Events\CashRegisterLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendKickNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CashRegisterLogin  $event
     * @return void
     */
    public function handle(CashRegisterLogin $event)
    {
        $event->original_user_id = $event->cashRegisterLogs->getOriginal('user_id');
        $event->new_user_id = $event->cashRegisterLogs->user_id;
    }
}
