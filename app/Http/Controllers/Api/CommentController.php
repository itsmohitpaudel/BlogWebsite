<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $post->comments()->with('user')->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $validatedData = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = new Comment($validatedData);
        $comment->user_id = Auth::id();
        $post->comments()->save($comment); //Here, commentable id and commentable_type are auto assigned

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $comment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->first();

        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('delete', $comment)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
            'data' => null
        ], 200);
    }
}
