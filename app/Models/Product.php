<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    //
    use HasFactory;
     protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'status',
        'user_id',
        'category_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

       public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

     public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

          return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeMinStock(Builder $query, $minStock): Builder
    {
        if ($minStock === null || $minStock === '') {
            return $query;
        }

        return $query->where('stock', '>=', $minStock);
    }

    public function scopePriceBetween(Builder $query, $minPrice = null, $maxPrice = null): Builder
    {
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeSort(Builder $query, ?string $sortBy, ?string $sortDirection): Builder
    {
        $allowedSorts = ['id', 'name', 'price', 'stock', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        return $query->orderBy($sortBy, $sortDirection);
    }
}
