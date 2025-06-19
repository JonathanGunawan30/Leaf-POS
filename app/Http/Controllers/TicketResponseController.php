<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResponseRequest;
use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketResponseController extends Controller
{
    public function store(StoreResponseRequest $request, Ticket $ticket)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'Admin' && $ticket->user_id !== $user->id) {
                return response()->json([
                    "errors" => [
                        'message' => 'You are not authorized to respond to this ticket.'
                    ],
                    'success' => false,
                ], 403);
            }

            $request->validated();

            $response = TicketResponse::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
            ]);

            if ($user->role === 'agent' && $ticket->status === 'open') {
                $ticket->status = 'in_progress';
                $ticket->save();
            }

            $response->load('user');
            $response->author = $response->user->name ?? 'Unknown User';
            unset($response->user);

            return response()->json([
                "data" => [
                    "response" => $response,
                ],
                'success' => true,
                'message' => 'Response added successfully!',
            ], 201);

        }  catch (\Exception $e) {
            Log::error('Error adding ticket response: ' . $e->getMessage(), ['ticket_id' => $ticket->id, 'user_id' => Auth::id(), 'request' => $request->all()]);
            return response()->json([
                "errors" => [
                    'message' => 'Failed to add response. Please try again later.'
                ],
                'success' => false
            ], 500);
        }
    }
}
