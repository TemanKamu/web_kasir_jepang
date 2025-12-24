<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $menu;
    public $action;

    public function __construct($menu, $action)
    {
        $this->menu = $menu;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return new Channel('menus');
    }

    public function broadcastAs()
    {
        return 'menu.updated';
    }

    public function broadcastWith()
    {
        if (is_array($this->menu)) {
            return [
                'id' => $this->menu['id'],
                'action' => $this->action
            ];
        }

        return [
            'id' => $this->menu->id,
            'name' => $this->menu->name,
            'desc' => $this->menu->desc,
            'price' => $this->menu->price,
            'image_url' => $this->menu->image_url,
            'category' => $this->menu->category?->name,
            'category_id' => $this->menu->category_id,
            'status' => $this->menu->status,
            'count_sold' => $this->menu->count_sold,
            'action' => $this->action
        ];
    }
}
