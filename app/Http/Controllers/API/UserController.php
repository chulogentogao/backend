<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (admin only).
     */
    public function index(Request $request)
    {
        try {
            $users = User::with('roles')->get();
            $users->each(function ($user) {
                $user->roles_array = $user->roles->pluck('name')->toArray();
            });
            
            return response()->json([
                'status' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user restriction (admin only).
     */
    public function toggleRestriction(Request $request, $id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Prevent admin from restricting themselves
            if ($user->id === $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot restrict yourself'
                ], 400);
            }

            $user->is_restricted = !$user->is_restricted;
            $user->save();

            $user->load('roles');
            $user->roles_array = $user->roles->pluck('name')->toArray();

            return response()->json([
                'status' => true,
                'message' => $user->is_restricted ? 'User restricted successfully' : 'User unrestricted successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to toggle user restriction: ' . $e->getMessage()
            ], 500);
        }
    }
}

