<?php

namespace App\Events;

use App\CashRegisterLogs;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashRegisterLogin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cashRegisterLogs;
    public $original_user_id;
    public $new_user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CashRegisterLogs $cashRegisterLogs)
    {
        $this->cashRegisterLogs = $cashRegisterLogs;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('user.' . $this->original_user_id),
            new PrivateChannel('user.' . $this->new_user_id),
        ];
    }
}
