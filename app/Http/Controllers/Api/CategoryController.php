<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::latest()->with('posts')->get();

        if ($category->isEmpty()) {
            return response()->json([
                'message' => 'No Categories Found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('create', Category::class)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
        ]);

        // Ensure slug is unique
        $validatedData['category_slug'] = Str::slug($validatedData['category_name']);

        // Check if the category already exists
        if (Category::where('category_slug', $validatedData['category_slug'])->exists()) {
            return response()->json([
                'message' => 'Category with this name already exists',
                'data' => null
            ], 409); // 409 Conflict
        }

        $category = Category::create([
            'category_name' => $validatedData['category_name'],
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $category = Category::where('category_slug', $slug)
            ->with(['posts'])
            ->first();

        // Checks if the category exists
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'data' => null
            ], 404);
        }

        // Checks authorization
        Gate::authorize('view', $category);

        // Returns cate$category
        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $category = Category::where('category_slug', $slug)->first();

        // If the category is not found
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('update', $category)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null,
            ], 403);
        }

        $validatedData = $request->validate([
            'category_name' => 'sometimes|required|string|max:255',
        ]);

        if (empty($validatedData)) {
            return response()->json([
                'message' => 'No data given for update',
                'data' => null
            ], 400);
        }

        $category->update($validatedData);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        // Finds the category
        $category = Category::where('category_slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'data' => null
            ], 404);
        }

        if (!Gate::allows('delete', $category)) {
            return response()->json([
                'message' => 'Unauthorized',
                'data' => null
            ], 403);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
            'data' => null
        ], 200);
    }
}
