<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;


class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        if ($perPage < 1 || $perPage > 50) {
            $perPage = 10;
        }

        $query = Product::query()
            ->where('user_id', $request->user()->id)
            ->search($request->get('search'))
            ->status($request->get('status'))
            ->minStock($request->get('min_stock'))
            ->priceBetween($request->get('min_price'), $request->get('max_price'))
            ->sort($request->get('sort_by'), $request->get('sort_direction'));

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        if ($request->boolean('only_active')) {
            $query->active();
        }

        $products = $query->paginate($perPage);

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
        $products = Product::create([$request->validated()
                        ,'user_id' => $request->user()->id]);
        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($products),
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
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
        $product->update($request->validated());

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
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
