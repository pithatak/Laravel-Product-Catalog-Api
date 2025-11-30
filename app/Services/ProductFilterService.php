<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductFilterService
{
    public function filter(Request $request)
    {
        $query = Product::query()->with('attributes');

            if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('manufacturer', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', $request->manufacturer);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_capacity') || $request->filled('max_capacity')) {
            $query->whereHas('attributes', function ($q) use ($request) {

                if ($request->filled('min_capacity')) {
                    $q->whereRaw("CAST(value AS DECIMAL(10,2)) >= ?", [$request->min_capacity]);
                }
                if ($request->filled('max_capacity')) {
                    $q->whereRaw("CAST(value AS DECIMAL(10,2)) <= ?", [$request->max_capacity]);
                }

            });
        }

        if ($request->filled('min_power') || $request->filled('max_power')) {
            $query->whereHas('attributes', function ($q) use ($request) {

                if ($request->filled('min_power')) {
                    $q->where('value', '>=', $request->min_power);
                }
                if ($request->filled('max_power')) {
                    $q->where('value', '<=', $request->max_power);
                }

            });
        }

        if ($request->filled('connector_type')) {
            $query->whereHas('attributes', function ($q) use ($request) {
                $q->where('value', $request->connector_type);
            });
        }

        return $query->get();
    }
}
