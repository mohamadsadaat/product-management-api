<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => CategoryResource::collection($categories),
        ], 200);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'status' => true,
            'message' => 'Category fetched successfully',
            'data' => new CategoryResource($category),
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
