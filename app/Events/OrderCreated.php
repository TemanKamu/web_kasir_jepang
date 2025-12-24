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

class OrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bill;

    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }

    public function broadcastOn()
    {
        return new Channel('orders');
    }

    public function broadcastAs()
    {
        return 'order.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->bill->id,
            'code' => $this->bill->code,
            'queue_number' => $this->bill->queue_number,
            'customer_name' => $this->bill->user?->name ?? 'Guest',
            'service_type' => $this->bill->service_type,
            'payment_method' => $this->bill->payment_method,
            'status' => $this->bill->status,
            'total' => $this->bill->orderedMenus->sum('total_price'),
            'items' => $this->bill->orderedMenus->map(function ($item) {
                return [
                    'menu_name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'price' => $item->menu->price,
                    'total' => $item->total_price
                ];
            }),
            'proof_image' => $this->bill->proofTransferPayment?->image_url,
            'date' => $this->bill->date->format('Y-m-d H:i:s')
        ];
    }
}
