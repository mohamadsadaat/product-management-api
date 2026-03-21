<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;


class productController extends Controller
{
    /**
     * Display a listing of the resource.
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

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
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
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
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

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product),
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return response()->json([
            'status' => true,
            'message' => 'Product fetched successfully',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
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
       
        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product moved to trash successfully',
        ], 200);
    }

    public function trash(Request $request)
    {
        $this->authorize('viewTrash', Product::class);

        $products = Product::onlyTrashed()
            ->with('category')
            ->where('user_id', $request->user()->id)
            ->latest('deleted_at')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Trashed products fetched successfully',
            'data' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ], 200);
    }

    public function restore(Request $request, Product $product)
    {
        $this->authorize('restore', $product);

        $product->restore();
        $product->load('category');

        return response()->json([
            'status' => true,
            'message' => 'Product restored successfully',
            'data' => new ProductResource($product),
        ], 200);
    }

    public function forceDelete(Request $request, Product $product)
    {
        $this->authorize('forceDelete', $product);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();

        return response()->json([
            'status' => true,
            'message' => 'Product permanently deleted successfully',
        ], 200);
    }
}
