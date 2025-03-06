<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Gate::allows('viewAny', User::class)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $users = User::latest()->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('create', User::class)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,author' //Admin assigns roles
        ]);

        // Hashing the entered passowrd
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $person = User::where('id', $id)->first();

        // Checks if the user exists
        if (!$person) {
            return response()->json([
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        // Checks authorization
        Gate::authorize('view', $person);

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $person
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        // If the user is not found
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('update', $user)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:6'
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    public function updateRole(Request $request, $id)
    {
        // Finds the user
        $person = User::where('id', $id)->first();

        $loggedInUser = Auth::id();

        // return response()->json([
        //     'messsage' => 'Hi',
        //     'data' => $person
        // ]);

        if (!$person) {
            return response()->json([
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('updateRole', $person)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $validatedData = $request->validate([
            'role' => 'required|in:admin,author'
            //Only valid roles allowed
        ]);

        // To prevent self-role change to avoid loosing admin role
        if (Auth::id() === $person->id) {
            return response()->json([
                'message' => 'You cannot change your own role',
                'data' => null
            ], 403);
        }

        // Updating the role
        $person->update(['role' => $validatedData['role']]);

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $person
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Finds the user
        $person = User::where('id', $id)->first();

        if (!$person) {
            return response()->json([
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('delete', $person)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $person->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'data' => null
        ], 204);
    }
}
