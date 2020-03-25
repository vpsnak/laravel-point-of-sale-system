<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeauth
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $msg;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, string $msg = null)
    {
        $this->user = $user;
        $this->msg = $msg;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $actions = [
            [
                'name' => 'logout',
                'root' => ['root' => true],
            ]
        ];

        return [
            'actions' => $actions,
            'notification' => [
                'msg' => $this->msg,
                'type' => 'warning'
            ]
        ];
    }
}
