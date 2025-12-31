<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type; // 'menu', 'category', atau 'subcategory'

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function broadcastOn(): array
    {
        // Kita pakai channel publik supaya semua yang buka web (Admin & User) dapet update
        return [
            new Channel('pos-data-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'data.changed';
    }
}
