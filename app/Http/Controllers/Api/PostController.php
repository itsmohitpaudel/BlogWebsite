<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = Post::latest()
            ->with('category', 'author', 'comments', 'comments.user', 'tags')
            ->get();

        if ($post->isEmpty()) {
            return response()->json([
                'message' => 'No Posts Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $post
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Post::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:published,draft'
        ]);

        $post = Post::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'author_id' => $request->user()->id, //Ensures the logged-in user is the author
            'status' => $validatedData['status']
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    public function postWiseTags($id)
    {
        $post = Post::where('id', $id)->first();

        if (!$post) {
            return response()->json([
                'message' => 'No Post Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Post wise tags retrieved successfully',
            'post' => $post,
            'data' => $post->tags()->get()
        ], 200);
    }

    public function postWiseComments($id)
    {
        $post = Post::where('id', $id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'No Post Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Post wise comments retrieved successfully',
            'post' => $post,
            'data' => $post->comments()
                ->with('user')
                ->with('commentable')
                ->get()
        ], 200);
    }

    public function attachTags(Request $request, $id)
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
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id'
        ]);


        $post->tags()->sync($validatedData['tags']);

        return response()->json([
            'message' => 'Tags attached successfully',
            'data' => $post->tags
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->with('category', 'author', 'comments', 'comments.user', 'tags')
            ->first();


        // Checks if the post exists
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'data' => null
            ], 404);
        }

        // Checks authorization
        Gate::authorize('view', $post);


        // Returns post with category, author and comments
        return response()->json([
            'message' => 'Post retrieved successfully',
            'data' => $post
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        // Finds post
        $post = Post::where('slug', $slug)->first();

        // If the post is not found
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'data' => null
            ], 404);
        }

        // Gate::authorize('update', $post);

        if (!Gate::allows('update', $post)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'sometimes|required|in:published,draft'
        ]);


        if (empty($validatedData)) {
            return response()->json([
                'message' => 'No data given for update',
                'data' => null
            ], 400);
        }

        $post->update($validatedData);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        // Finds the post
        $post = Post::where('slug', $slug)->first();

        // Gate::authorize('delete', $post);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('delete', $post)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
            'data' => null
        ], 200);
    }
}
