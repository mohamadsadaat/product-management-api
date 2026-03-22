<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="List categories",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Categories fetched successfully")
     * )
     */
    public function index()
    {
        $categories = Category::latest()->get();

        return $this->successResponse('Categories fetched successfully', CategoryResource::collection($categories));
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create category",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created successfully")
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->successResponse('Category created successfully', new CategoryResource($category), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{category}",
     *     summary="Get single category",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category fetched successfully"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Category $category)
    {
        return $this->successResponse('Category fetched successfully', new CategoryResource($category));
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{category}",
     *     summary="Update category",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully")
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse('Category updated successfully', new CategoryResource($category));
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{category}",
     *     summary="Delete category",
     *     tags={"Categories"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted successfully")
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse('Category deleted successfully');
    }
}
