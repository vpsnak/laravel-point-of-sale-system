<?php

namespace App\Events;

use App\User;
use App\CashRegisterLogs;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashRegisterLogin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cashRegisterLogs;
    public $loggedUser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CashRegisterLogs $cashRegisterLogs, User $loggedUser)
    {
        $this->cashRegisterLogs = $cashRegisterLogs;
        $this->loggedUser = $loggedUser;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->cashRegisterLogs->user_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $cashReg = $this->cashRegisterLogs->cash_register()->first();
        $mutations = [
            [
                'name' => 'setStore',
                'data' => null,
                'root' => ['root' => true],
            ], [
                'name' => 'setCashRegister',
                'data' => null,
                'root' => ['root' => true],
            ]
        ];


        return [
            'mutations' => $mutations,
            'notification' => [
                'msg' => "<b>{$this->loggedUser->name}</b> logged into the cash register
                    <br>Your session with cash register <b>{$cashReg->name}</b> terminated",
                'type' => 'warning'
            ]
        ];
    }
}
