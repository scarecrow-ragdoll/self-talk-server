<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Return a list of all registered users.
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'username', 'email', 'created_at')->get();

        return response()->json($users);
    }
}
