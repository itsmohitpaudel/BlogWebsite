<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comment = Comment::with(
            'commentable'
        )->paginate(10);

        if ($comment->isEmpty()) {
            return response()->json([
                'message' => 'No Comments Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Comments retrieved successfully',
            'data' => $comment
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $post = Post::where('id', $id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'data' => null
            ], 404);
        }

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
    public function show($id)
    {
        try {
            $comment = Comment::with(['commentable', 'user'])->findOrFail($id);
            Gate::authorize('view', $comment);

            // Returns comment
            return response()->json([
                'message' => 'Comment retrieved successfully',
                'data' => $comment
            ], 200);
        }
        // Checks if comment exists
        catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Comment not found',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myComments()
    {
        $comments = Comment::where('user_id', Auth::id())
            ->with(
                'commentable',
            )
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => $comments->isEmpty() ? 'No Comments Found' : 'My Comments retrieved successfully',
            'data' => $comments
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::where('id', $id)->first();

        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('update', $comment)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update($validatedData);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $comment
        ], 200);
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
