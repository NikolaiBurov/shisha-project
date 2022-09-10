<?php

namespace App\Events\Cart;

use App\Models\Cart;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Update
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Cart
     */
    public Cart $cartEntity;

    /**
     * Create a new event instance.
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cartEntity)
    {
        $this->cartEntity = $cartEntity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
