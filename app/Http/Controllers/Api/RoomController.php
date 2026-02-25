<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\IndexRoomRequest;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Return rooms that the authenticated user belongs to.
     */
    public function index(IndexRoomRequest $request): JsonResponse
    {
        $rooms = $request->user()
            ->rooms()
            ->with(['creator:id,name,username', 'users:id,name,username'])
            ->latest()
            ->get();

        return response()->json($rooms);
    }

    /**
     * Create a new room and attach the creator as an admin member.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['created_by'] = $request->user()->id;

        $room = Room::create($data);

        // Attach the creator as admin
        $room->users()->attach($request->user()->id, [
            'role'      => 'admin',
            'joined_at' => now(),
        ]);

        $room->load(['creator:id,name,username', 'users:id,name,username']);

        return response()->json($room, 201);
    }
}
