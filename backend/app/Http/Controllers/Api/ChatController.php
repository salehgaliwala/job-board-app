<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::where('employer_id', $user->id)
            ->orWhere('seeker_id', $user->id)
            ->with(['employer', 'seeker', 'jobListing'])
            ->with([
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'employer') {
            return response()->json(['message' => 'Only employers can initiate a chat.'], 403);
        }

        $request->validate([
            'seeker_id' => 'required|exists:users,id',
            'job_listing_id' => 'nullable|exists:job_listings,id',
            'message' => 'required|string',
        ]);

        // Check if conversation already exists
        $conversation = Conversation::where('employer_id', $user->id)
            ->where('seeker_id', $request->seeker_id)
            ->where('job_listing_id', $request->job_listing_id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'employer_id' => $user->id,
                'seeker_id' => $request->seeker_id,
                'job_listing_id' => $request->job_listing_id,
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'content' => $request->input('message'),
        ]);

        return response()->json([
            'conversation' => $conversation->load(['employer', 'seeker', 'jobListing']),
            'message' => $message
        ], 201);
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        if ($conversation->employer_id !== $user->id && $conversation->seeker_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if ($conversation->employer_id !== $user->id && $conversation->seeker_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        return response()->json($message, 201);
    }
}
