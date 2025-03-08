<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()
            ->with([
                'category',
                'author',
                'tags',
                'comments.user' => function ($query) {
                    $query->paginate(5);
                },
            ])
            ->paginate(10);

        // Per post pagination for comments: Tried but not used here in this case
        // This approach loads and paginates all comments for each post 
        //  Therefore, I fetched comments separately on api {"posts/post_id/comments"}

        // $posts->getCollection()->transform(function ($post) use ($request) {
        //     $comments = $post->comments()
        //         ->with('user') // Eager load user for each comment
        //         ->latest()
        //         ->paginate(5, ['*'], 'comment_page') // Separate pagination for comments
        //         ->appends($request->query()); // Maintain query parameters

        //     $post->comments = $comments;
        //     return $post;
        // });

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No Posts Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts
        ], 200);
    }

    public function search(Request $request)
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters([
                'title',  // Filtering posts by title
                AllowedFilter::scope('category'), // Filter posts by category name
                AllowedFilter::scope('author'), // Filter posts by author name

                // Tags are store in different table
                AllowedFilter::scope('tag'), // Filter posts by tag name
            ])
            ->with(['category', 'author', 'tags', 'comments.user'])  // Eager loading
            ->paginate(5);

        // Check if any posts were found
        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found matching the search criteria.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Posts retrieved successfully',
            'data' => $posts
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
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:published,draft'
        ]);

        $post = Post::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'author_id' => $request->user()->id, //Ensures the logged-in user is the author
            'status' => $validatedData['status']
        ])->tags()->sync($validatedData['tags']);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    public function postWiseTags($id)
    {
        $post = Post::with('tags')->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'No Post Found',
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => 'Post wise tags retrieved successfully',
            'post' => $post,
        ], 200);
    }

    public function postWiseComments($id)
    {
        // Get post along with Post author
        $post = Post::with('author')
            ->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'No Post Found',
                'data' => null
            ], 200);
        }

        // Paginate the comments to control loading all comments at once
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate(5);

        return response()->json([
            'message' => 'Post-wise comments retrieved successfully',
            'post' => $post,
            'data' => $comments
        ], 200);
    }

    public function myPosts()
    {
        $post = Post::where('author_id', Auth::id())
            ->with(
                'category',
                // 'author',
                'tags',
                'comments',
                'comments.user',
            )
            ->latest()
            ->paginate(5);

        if ($post->isEmpty()) {
            return response()->json([
                'message' => 'No Post Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'My Posts retrieved successfully',
            'data' => $post
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
        $post = Post::where('slug', $slug)
            ->with('category', 'author', 'comments', 'comments.user', 'tags')
            ->first();

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
            'tags' => 'sometimes|required|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'sometimes|required|in:published,draft'
        ]);


        // Check if there are any data to update
        if (empty($validatedData)) {
            return response()->json([
                'message' => 'No data given for update',
                'data' => null
            ], 400);
        }

        $post->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'author_id' => $request->user()->id, //Ensures the logged-in user is the author
            'status' => $validatedData['status']
        ]);

        // If tags are in the request, sync them
        if (isset($validatedData['tags']) && !empty($validatedData['tags'])) {
            $post->tags()->sync($validatedData['tags']);
        }

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

        // Delete related comments
        $post->comments()->delete();

        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
            'data' => null
        ], 200);
    }
}
