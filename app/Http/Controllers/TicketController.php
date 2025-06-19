<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignAgentRequest;
use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Ticket::query();

            if ($user->role === 'Admin') {
                $query->where('user_id', $user->id);
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('subject', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }

            $tickets = $query->orderBy('created_at', 'desc')->get();
            $tickets->load(["user", 'responses.user']);
            return response()->json([
                "data" => [
                    "ticket" => $tickets
                ],
                'success' => true,
                'message' => 'Tickets fetched successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching tickets: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return response()->json([
                "errors" => [
                    'message' => 'Failed to fetch tickets. Please try again later.'
                ],
                'success' => false,
            ], 500);
        }
    }

    public function store(CreateTicketRequest $request)
    {
        try {
            $user = Auth::user();

            $request->validated();

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'subject' => $request->subject,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'status' => 'open',
            ]);

            return response()->json([
                "data" => [
                    "ticket" => $ticket
                ],
                "message" => "Ticket created successfully.",
                "success" => true
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating ticket: ' . $e->getMessage(), ['user_id' => Auth::id(), 'request' => $request->all()]);
            return response()->json([
                "errors" => [
                    'message' => 'Failed to create ticket. Please try again later.'
                ],
                'success' => false,
            ], 500);
        }
    }

    public function show(Ticket $ticket)
    {
        try {
            $user = Auth::user();

            $userRoleName = $user->role->name ?? null;

            if ($userRoleName !== 'Admin' && $ticket->user_id !== $user->id) {
                return response()->json([
                    "errors" => [
                        "message" => "You do not have permission to view this ticket."
                    ],
                    'success' => false,
                ], 403);
            }

            $ticket->load(['responses.user', "user"]);

            $ticket->responses->each(function ($response) {
                $response->author = $response->user->name ?? 'Unknown User';
                unset($response->user);
            });


            return response()->json([
                "data" => [
                    "ticket" => $ticket
                ],
                "message" => "Ticket fetched successfully.",
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching ticket: ' . $e->getMessage(), ['ticket_id' => $ticket->id, 'user_id' => Auth::id()]);
            return response()->json([
                "errors" => [
                    "message" => "Failed to fetch ticket. Please try again later."
                ],
                'success' => false,
            ], 500);
        }
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket)
    {
        try {
            $user = Auth::user();

            if (!$user->role || $user->role->name !== 'Admin') {
                return response()->json([
                    "errors" => [
                        "message" => "You do not have permission to update ticket status."
                    ],
                    'success' => false
                ], 403);
            }

            $request->validated();

            $ticket->status = $request->status;
            if (in_array($request->status, ['resolved', 'closed']) && is_null($ticket->closed_at)) {
                $ticket->closed_at = now();
            } elseif (!in_array($request->status, ['resolved', 'closed'])) {
                $ticket->closed_at = null;
            }

            $ticket->save();

            return response()->json([
                "data" => [
                    "ticket" => $ticket
                ],
                'success' => true,
                'message' => 'Ticket status updated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating ticket status: ' . $e->getMessage(), ['ticket_id' => $ticket->id, 'user_id' => Auth::id()]);
            return response()->json([
                "errors" => [
                    "message" => "Failed to update ticket status. Please try again later."
                ],
                'success' => false,
            ], 500);
        }
    }

    public function assignAgent(AssignAgentRequest $request, Ticket $ticket)
    {
        try {
            $user = Auth::user();


            if ($user->role !== 'Admin') {
                return response()->json([
                    "errors" => [
                        "message" => "ou are not authorized to assign agents."
                    ],
                    'success' => false,
                ], 403);
            }

            $request->validated();

            $ticket->assigned_to = $request->agent_id;
            $ticket->save();

            return response()->json([
                "data" => [
                    "ticket" => $ticket
                ],
                'success' => true,
                'message' => 'Ticket assigned successfully!',
            ]);

        }  catch (\Exception $e) {
            Log::error('Error assigning agent to ticket: ' . $e->getMessage(), ['ticket_id' => $ticket->id, 'user_id' => Auth::id()]);
            return response()->json([
                "errors" => [
                    "message" => "Failed to assign agent to ticket. Please try again later."
                ],
                'success' => false,
            ], 500);
        }
    }
}
