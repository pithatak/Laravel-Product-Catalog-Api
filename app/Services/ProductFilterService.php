<?php

namespace App\Services;

use App\Models\Product;

class ProductFilterService
{
    public function filter($request)
    {
        $query = Product::query()->with('attributes');

        // Category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Manufacturer
        if ($request->manufacturer) {
            $query->where('manufacturer', $request->manufacturer);
        }

        // Price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Fulltext-like search: name, manufacturer, description
        if ($request->search) {
            $s = '%' . $request->search . '%';

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('manufacturer', 'like', $s)
                    ->orWhere('description', 'like', $s);
            });
        }

        // Category-specific filters
        if ($request->category === 'battery') {
            $this->filterAttribute($query, 'capacity', $request->capacity_min, $request->capacity_max);
        }

        if ($request->category === 'panel') {
            $this->filterAttribute($query, 'power_output', $request->power_min, $request->power_max);
        }

        if ($request->category === 'connector' && $request->connector_type) {
            $this->filterAttributeExact($query, 'connector_type', $request->connector_type);
        }

        return $query->get();
    }

    private function filterAttribute($query, $key, $min, $max)
    {
        if ($min !== null) {
            $query->whereHas('attributes', function ($q) use ($key, $min) {
                $q->where('key', $key)->where('value', '>=', $min);
            });
        }

        if ($max !== null) {
            $query->whereHas('attributes', function ($q) use ($key, $max) {
                $q->where('key', $key)->where('value', '<=', $max);
            });
        }
    }

    private function filterAttributeExact($query, $key, $value)
    {
        $query->whereHas('attributes', function ($q) use ($key, $value) {
            $q->where('key', $key)->where('value', $value);
        });
    }
}
