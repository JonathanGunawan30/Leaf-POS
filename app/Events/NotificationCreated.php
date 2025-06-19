<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'notification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'data' => $this->notification->data,
            'timestamp' => $this->notification->created_at->timestamp * 1000
        ];
    }
}

// === Stock Alert Event ===
class StockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $notification;

    public function __construct($product, Notification $notification = null)
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

// === Global Notification Event ===
class GlobalNotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new Channel('global.notifications');
    }

    public function broadcastAs()
    {
        return 'global-notification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'data' => $this->notification->data,
            'timestamp' => $this->notification->created_at->timestamp * 1000
        ];
    }
}
