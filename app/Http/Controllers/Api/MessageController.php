<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\IndexMessageRequest;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Return paginated messages for a room.
     * The authenticated user must be a member of the room.
     */
    public function index(IndexMessageRequest $request, Room $room): JsonResponse
    {
        $this->authorizeMembership($request, $room);

        $messages = $room->messages()
            ->with('user:id,name,username')
            ->latest()
            ->paginate(50);

        return response()->json($messages);
    }

    /**
     * Store a new message in a room.
     * The authenticated user must be a member of the room.
     */
    public function store(StoreMessageRequest $request, Room $room): JsonResponse
    {
        $this->authorizeMembership($request, $room);

        $data = $request->validated();

        $message = $room->messages()->create([
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        $message->load('user:id,name,username');

        // TODO: broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    /**
     * Abort with 403 if the authenticated user is not a member of the room.
     */
    private function authorizeMembership(IndexMessageRequest|StoreMessageRequest $request, Room $room): void
    {
        $isMember = $room->users()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless($isMember, 403, 'You are not a member of this room.');
    }
}
