<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NotificationCreated;
use App\Events\StockAlert;
use App\Events\GlobalNotificationCreated;

class NotificationService
{
    /**
     * Create notification for specific user
     */
    public function createForUser($userId, $type, $title, $message, $data = null, $broadcast = true)
    {
        $notification = Notification::createForUser($userId, $type, $title, $message, $data);

        if ($broadcast) {
            broadcast(new NotificationCreated($notification))->toOthers();
        }

        return $notification;
    }

    /**
     * Create global notification (for all users)
     */
    public function createGlobal($type, $title, $message, $data = null, $broadcast = true)
    {
        $notification = Notification::createGlobal($type, $title, $message, $data);

        if ($broadcast) {
            broadcast(new GlobalNotificationCreated($notification))->toOthers();
        }

        return $notification;
    }

    /**
     * Create stock alert notification
     */
    public function createStockAlert($product, $userId = null, $broadcast = true)
    {
        $notification = Notification::createStockAlert($product, $userId);

        if ($broadcast) {
            broadcast(new StockAlert($product, $notification))->toOthers();
        }

        return $notification;
    }

    /**
     * Create new order notification
     */
    public function createNewOrderNotification($order, $userId = null)
    {
        return $this->createForUser(
            $userId,
            'new-order',
            'Pesanan Baru!',
            "Pesanan baru #{$order->id} telah diterima",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number ?? $order->id,
                'total' => $order->total,
                'customer' => $order->customer?->name
            ]
        );
    }

    /**
     * Create order completed notification
     */
    public function createOrderCompletedNotification($order, $userId = null)
    {
        return $this->createForUser(
            $userId,
            'order-completed',
            'Pesanan Selesai!',
            "Pesanan #{$order->id} telah selesai diproses",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number ?? $order->id,
                'total' => $order->total,
                'customer' => $order->customer?->name
            ]
        );
    }

    /**
     * Create payment received notification
     */
    public function createPaymentReceivedNotification($payment, $userId = null)
    {
        return $this->createForUser(
            $userId,
            'payment-received',
            'Pembayaran Diterima!',
            "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah diterima",
            [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'method' => $payment->method,
                'reference' => $payment->reference
            ]
        );
    }

    /**
     * Create user activity notification
     */
    public function createUserActivityNotification($message, $userId = null, $data = null)
    {
        return $this->createForUser(
            $userId,
            'user-activity',
            'Aktivitas Pengguna',
            $message,
            $data
        );
    }

    /**
     * Create system notification
     */
    public function createSystemNotification($title, $message, $userId = null, $data = null)
    {
        return $this->createForUser(
            $userId,
            'system',
            $title,
            $message,
            $data
        );
    }

    /**
     * Bulk create notifications for multiple users
     */
    public function createBulkNotifications(array $userIds, $type, $title, $message, $data = null)
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->createForUser($userId, $type, $title, $message, $data, false);
        }

        // Broadcast all at once (optional optimization)
        foreach ($notifications as $notification) {
            broadcast(new NotificationCreated($notification))->toOthers();
        }

        return $notifications;
    }

    /**
     * Check and create stock alerts for products below threshold
     */
    public function checkStockAlerts()
    {
        // Assuming you have Product model with stock and stock_alert columns
        $lowStockProducts = \App\Models\Product::whereColumn('stock', '<=', 'stock_alert')
            ->where('stock_alert', '>', 0)
            ->get();

        $notifications = [];

        foreach ($lowStockProducts as $product) {
            // Check if alert already sent recently (avoid spam)
            $recentAlert = Notification::where('type', 'stock-alert')
                ->where('data->product_id', $product->id)
                ->where('created_at', '>=', now()->subHours(1))
                ->exists();

            if (!$recentAlert) {
                $notifications[] = $this->createStockAlert($product);
            }
        }

        return $notifications;
    }

    /**
     * Clean old notifications (keep only last 100 per user)
     */
    public function cleanOldNotifications($keepCount = 100)
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($notifications->count() > $keepCount) {
                $toDelete = $notifications->skip($keepCount);
                Notification::whereIn('id', $toDelete->pluck('id'))->delete();
            }
        }

        // Clean global notifications
        $globalNotifications = Notification::whereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($globalNotifications->count() > $keepCount) {
            $toDelete = $globalNotifications->skip($keepCount);
            Notification::whereIn('id', $toDelete->pluck('id'))->delete();
        }
    }
}
