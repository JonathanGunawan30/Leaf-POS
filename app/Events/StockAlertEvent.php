<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAlertEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $product;
    public $notification;

    public function __construct($product, $notification = null)
    {
        $this->product = $product;
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new Channel('stock.alert');
    }

    public function broadcastAs()
    {
        return 'stock-alert';
    }

    public function broadcastWith()
    {
        return [
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'stock' => $this->product->stock,
                'stock_alert' => $this->product->stock_alert
            ],
            'notification_id' => $this->notification?->id,
            'timestamp' => now()->timestamp * 1000
        ];
    }
}
