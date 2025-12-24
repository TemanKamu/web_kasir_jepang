<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bill;
    public $oldStatus;
    public $newStatus;

    public function __construct($bill, $oldStatus, $newStatus)
    {
        $this->bill = $bill;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        return new Channel('orders');
    }

    public function broadcastAs()
    {
        return 'order.status.updated';
    }

    public function broadcastWith()
    {
        if (is_array($this->bill)) {
            return [
                'id' => $this->bill['id'],
                'status' => $this->newStatus,
                'old_status' => $this->oldStatus
            ];
        }

        return [
            'id' => $this->bill->id,
            'code' => $this->bill->code,
            'queue_number' => $this->bill->queue_number,
            'status' => $this->newStatus,
            'old_status' => $this->oldStatus,
            'date' => $this->bill->date->format('Y-m-d H:i:s')
        ];
    }
}
