<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $query = Notification::forUser($user->id)
                ->orderBy('created_at', 'desc');

            // Filter by type if provided
            if ($request->has('type') && $request->type) {
                $query->byType($request->type);
            }

            // Filter by read status
            if ($request->has('read')) {
                if ($request->read === 'true' || $request->read === '1') {
                    $query->read();
                } elseif ($request->read === 'false' || $request->read === '0') {
                    $query->unread();
                }
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->paginate($perPage);

            // Transform data to match frontend format
            $transformedNotifications = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'read' => $notification->read,
                    'timestamp' => $notification->created_at->timestamp * 1000, // Convert to milliseconds for JS
                    'created_at' => $notification->created_at->toISOString(),
                    'read_at' => $notification->read_at?->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data' => $transformedNotifications,
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'unread_count' => Notification::forUser($user->id)->unread()->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = Notification::forUser($user->id)->unread()->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $notification = Notification::forUser($user->id)->findOrFail($id);

            if (!$notification->read) {
                $notification->markAsRead();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => [
                    'id' => $notification->id,
                    'read' => $notification->read,
                    'read_at' => $notification->read_at?->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for authenticated user
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();

            $updatedCount = Notification::forUser($user->id)
                ->unread()
                ->update([
                    'read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'data' => [
                    'updated_count' => $updatedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete specific notification
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();

            $notification = Notification::forUser($user->id)->findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all notifications for authenticated user
     */
    public function clearAll(): JsonResponse
    {
        try {
            $user = Auth::user();

            $deletedCount = Notification::forUser($user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared',
                'data' => [
                    'deleted_count' => $deletedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear all notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create notification (for testing or admin use)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'user_id' => 'nullable|exists:users,id',
                'data' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $notification = Notification::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'title' => $request->title,
                'message' => $request->message,
                'data' => $request->data
            ]);

            // Broadcast notification if using Pusher/Laravel Echo
            if ($request->user_id) {
                broadcast(new \App\Events\NotificationCreated($notification))->toOthers();
            } else {
                // Global notification
                broadcast(new \App\Events\GlobalNotificationCreated($notification))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
                'data' => [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'timestamp' => $notification->created_at->timestamp * 1000
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle webhook notifications from external services
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            // Validate the webhook payload
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'target_user_id' => 'nullable|exists:users,id',
                'data' => 'nullable|array',
                'webhook_key' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify webhook key for security
            $validKey = env('NOTIFICATION_WEBHOOK_KEY');
            if (!$validKey || $request->webhook_key !== $validKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook key'
                ], 403);
            }

            // Create the notification
            $notification = Notification::create([
                'user_id' => $request->target_user_id,
                'type' => $request->type,
                'title' => $request->title,
                'message' => $request->message,
                'data' => $request->data
            ]);

            // Broadcast if needed
            if ($request->target_user_id) {
                broadcast(new \App\Events\NotificationCreated($notification))->toOthers();
            } else {
                broadcast(new \App\Events\GlobalNotificationCreated($notification))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook notification processed successfully',
                'data' => [
                    'id' => $notification->id
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
