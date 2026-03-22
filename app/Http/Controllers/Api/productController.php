<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class productController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="search", in="query", description="Search by name or description", @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status", @OA\Schema(type="string", enum={"active","inactive"})),
     *     @OA\Parameter(name="category_id", in="query", description="Filter by category", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_stock", in="query", description="Minimum stock", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_price", in="query", description="Minimum price", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="max_price", in="query", description="Maximum price", @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="sort_by", in="query", description="Sort field", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_direction", in="query", description="Sort direction", @OA\Schema(type="string", enum={"asc","desc"})),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Products fetched successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $perPage = (int) $request->get('per_page', 10);

        if ($perPage < 1 || $perPage > 50) {
            $perPage = 10;
        }

        $query = Product::query()
            ->with('category')
            ->where('user_id', $request->user()->id)
            ->search($request->get('search'))
            ->status($request->get('status'))
            ->minStock($request->get('min_stock'))
            ->when($request->get('category_id'), function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->priceBetween($request->get('min_price'), $request->get('max_price'))
            ->sort($request->get('sort_by'), $request->get('sort_direction'));

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        if ($request->boolean('only_active')) {
            $query->active();
        }

        $products = $query->with('category')->paginate($perPage);

        return $this->successResponse(
            'Products fetched successfully',
            ProductResource::collection($products->items()),
            200,
            [
                'filters' => [
                    'search' => $request->get('search'),
                    'status' => $request->get('status'),
                    'min_stock' => $request->get('min_stock'),
                    'min_price' => $request->get('min_price'),
                    'max_price' => $request->get('max_price'),
                    'in_stock' => $request->boolean('in_stock'),
                    'only_active' => $request->boolean('only_active'),
                    'sort_by' => $request->get('sort_by', 'created_at'),
                    'category_id' => $request->get('category_id'),
                    'sort_direction' => $request->get('sort_direction', 'desc'),
                    'per_page' => $perPage,
                ],
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"name","price","stock","status"},
     *
     *                 @OA\Property(property="name", type="string", example="Laptop"),
     *                 @OA\Property(property="sku", type="string", example="LAPTOP-001"),
     *                 @OA\Property(property="description", type="string", example="Gaming laptop"),
     *                 @OA\Property(property="price", type="number", format="float", example=1200.50),
     *                 @OA\Property(property="stock", type="integer", example=5),
     *                 @OA\Property(property="status", type="string", enum={"active","inactive"}),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Product created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        $product->load('category');

        return $this->successResponse('Product created successfully', new ProductResource($product), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     summary="Get single product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Product fetched successfully"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return $this->successResponse('Product fetched successfully', new ProductResource($product));
    }

    /**
     * @OA\Post(
     *     path="/api/products/{product}",
     *     summary="Update product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="price", type="number", format="float"),
     *                 @OA\Property(property="stock", type="integer"),
     *                 @OA\Property(property="status", type="string", enum={"active","inactive"}),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="_method", type="string", example="PUT")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Product updated successfully")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->load('category');

        return $this->successResponse('Product updated successfully', new ProductResource($product));
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}",
     *     summary="Soft delete product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Product moved to trash successfully")
     * )
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return $this->successResponse('Product moved to trash successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/products/trash",
     *     summary="List trashed products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Trashed products fetched successfully")
     * )
     */
    public function trash(Request $request)
    {
        $this->authorize('viewTrash', Product::class);

        $products = Product::onlyTrashed()
            ->with('category')
            ->where('user_id', $request->user()->id)
            ->latest('deleted_at')
            ->paginate(10);

        return $this->successResponse(
            'Trashed products fetched successfully',
            ProductResource::collection($products->items()),
            200,
            [
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products/{product}/restore",
     *     summary="Restore trashed product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Product restored successfully")
     * )
     */
    public function restore(Request $request, Product $product)
    {
        $this->authorize('restore', $product);

        $product->restore();
        $product->load('category');

        return $this->successResponse('Product restored successfully', new ProductResource($product));
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}/force",
     *     summary="Force delete trashed product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Product permanently deleted successfully")
     * )
     */
    public function forceDelete(Request $request, Product $product)
    {
        $this->authorize('forceDelete', $product);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();

        return $this->successResponse('Product permanently deleted successfully');
    }
}
