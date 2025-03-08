<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tag = Tag::latest()->get();

        if ($tag->isEmpty()) {
            return response()->json([
                'message' => 'No Tags Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Tags retrieved successfully',
            'data' => $tag
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('create', Tag::class)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'tag_name' => 'required|string|max:255|unique:tags,tag_name',
        ]);

        // Ensure slug is unique
        $validatedData['tag_slug'] = Str::slug($validatedData['tag_name']);

        // Check if the tag already exists
        if (Tag::where('tag_slug', $validatedData['tag_slug'])->exists()) {
            return response()->json([
                'message' => 'Tag with this name already exists',
                'data' => null
            ], 409); // 409 Conflict
        }

        $tag = Tag::create([
            'tag_name' => $validatedData['tag_name'],
        ]);

        return response()->json([
            'message' => 'Tag created successfully',
            'data' => $tag
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $tag = Tag::where('tag_slug', $slug)->first();

        // Checks if the tag exists
        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found',
                'data' => null
            ], 404);
        }

        // Checks authorization
        Gate::authorize('view', $tag);

        // Returns tag
        return response()->json([
            'message' => 'Tag retrieved successfully',
            'data' => $tag
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $tag = Tag::where('tag_slug', $slug)->first();

        // If the tag is not found
        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('update', $tag)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'tag_name' => 'sometimes|required|string|max:255',
        ]);

        if (empty($validatedData)) {
            return response()->json([
                'message' => 'No data given for update',
                'data' => null
            ], 400);
        }

        $tag->update($validatedData);

        return response()->json([
            'message' => 'Tag updated successfully',
            'data' => $tag
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        // Finds the tag
        $tag = Tag::where('tag_slug', $slug)->first();

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('delete', $tag)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
            'data' => null
        ], 200);
    }
}
