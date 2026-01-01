<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class OrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cartGroup; // Properti harus PUBLIC

    public function __construct($cartGroup)
    {
        // Gunakan load agar data menu ikut terkirim
        $this->cartGroup = $cartGroup->load('items.menu');
    }

    public function broadcastOn(): array
    {
        return [new \Illuminate\Broadcasting\Channel('orders')];
    }

    public function broadcastAs()
    {
        return 'new-order';
    }
}