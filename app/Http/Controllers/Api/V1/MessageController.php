<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends BaseApiController
{
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'property'])
            ->latest()
            ->get();

        $conversationPartners = [];

        foreach ($messages as $msg) {
            $otherId = $msg->sender_id === $userId ? $msg->receiver_id : $msg->sender_id;
            if (!isset($conversationPartners[$otherId])) {
                $otherUser = $msg->sender_id === $userId ? $msg->receiver : $msg->sender;
                $unread = Message::where('sender_id', $otherId)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                $conversationPartners[$otherId] = [
                    'partner' => $otherUser,
                    'last_message' => $msg,
                    'unread_count' => $unread,
                ];
            }
        }

        return $this->sendResponse(array_values($conversationPartners));
    }

    public function conversation(Request $request, int $otherUserId): JsonResponse
    {
        $userId = $request->user()->id;

        // Mark incoming messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $userId)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($userId, $otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $userId);
        })
        ->with(['property'])
        ->orderBy('created_at', 'asc')
        ->get();

        $partner = User::find($otherUserId);

        return $this->sendResponse([
            'partner' => $partner,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $sender = $request->user();

        $msg = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'property_id' => $request->property_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Push notification
        Notification::create([
            'user_id' => $request->receiver_id,
            'title' => "New message from {$sender->name}",
            'message' => Str::limit($request->message, 80),
            'type' => 'chat_message',
            'link' => "/messages/{$sender->id}",
            'data' => ['sender_id' => $sender->id],
        ]);

        return $this->sendResponse($msg->load(['property', 'sender']), 'Message sent', 201);
    }
}
