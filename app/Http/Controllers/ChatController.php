<?php

namespace App\Http\Controllers;

use App\Domain\Chat\Entities\Conversation;
use App\Domain\Chat\Entities\Message;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function requestChat(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'specialist_id' => 'nullable|exists:specialists,id',
        ]);

        $conversation = Conversation::create([
            'service_id' => $data['service_id'] ?? null,
            'specialist_id' => $data['specialist_id'] ?? null,
            'created_by_user_id' => auth()->id(),
            'status' => 'requested',
        ]);

        // TODO: Emit event to notify representatives

        return new ConversationResource($conversation->load(['service', 'specialist']));
    }

    public function acceptChat(Request $request, Conversation $conversation)
    {
        if (!$conversation->canBeAccepted()) {
            return response()->json(['error' => 'Chat cannot be accepted'], 422);
        }

        $conversation->accept(auth()->id());

        return new ConversationResource($conversation->load(['service', 'specialist']));
    }

    public function conversations(Request $request)
    {
        $conversations = Conversation::query()
            ->where(function ($q) {
                $q->where('created_by_user_id', auth()->id())
                  ->orWhere('representative_user_id', auth()->id());
            })
            ->with(['service', 'specialist'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return ConversationResource::collection($conversations);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->paginate(50);

        return MessageResource::collection($messages);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('message', $conversation);

        $data = $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'nullable|string|in:text,media',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => auth()->id(),
            'type' => $data['type'] ?? 'text',
            'content' => $data['content'],
        ]);

        $conversation->touch(); // Update conversation updated_at

        return new MessageResource($message->load('sender'));
    }
}
