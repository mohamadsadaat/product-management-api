<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'is_owner' => $this->user_id === auth()->id(),
            'image' => $this->image,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ] : null;
            }),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'is_trashed' => $this->trashed(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
