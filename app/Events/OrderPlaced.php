<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Properti harus public agar bisa diserialisasi oleh Pusher/Echo
    public $bill;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Bill  $bill
     */
    public function __construct(Bill $bill)
    {
        // Load relasi orderedMenus (bukan items) agar data menu ikut terkirim ke kasir
        $this->bill = $bill->load('orderedMenus.menu');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Channel yang didengarkan oleh Admin/Kasir
        return [new Channel('orders')];
    }

    /**
     * Nama event yang didengarkan di JavaScript (.listen('.new-order', ...))
     */
    public function broadcastAs()
    {
        return 'new-order';
    }

    /**
     * (Opsional) Menentukan data spesifik yang dikirim
     */
    public function broadcastWith()
    {
        return [
            'bill' => $this->bill
        ];
    }
}