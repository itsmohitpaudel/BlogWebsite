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
        return response()->json(Post::with(
            'category',
            'author'
        )->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // if (!Gate::allows('create', Post::class)) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        Gate::authorize('create', Post::class);

        // // Ensure user is authenticated
        // if (!$request->user()) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }


        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            // 'author_id' => 'required|int',
            'status' => 'required|in:published,draft'
        ]);

        Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'author_id' => $request->user()->id,
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Post created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->with('category')
            ->with('author')
            ->firstOrFail();

        // if (Gate::allows('view', $post)) {
        //     return response()->json($post, 200);
        // }

        Gate::authorize('view', $post);

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // if (!Gate::allows('update', $post)) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        Gate::authorize('update', $post);

        // return response()->json($post);

        // \Log::info('Incoming PATCH Data', ['request' => $request->all()]);

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'sometimes|required|in:published,draft'
        ]);


        if (!empty($validatedData)) {
            $post->update($validatedData);
        } else {
            return response()->json(['message' => 'No data provided for update'], 400);
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
