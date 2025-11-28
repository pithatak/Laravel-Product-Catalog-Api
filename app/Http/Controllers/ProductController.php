<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ProductFilterService $filterService)
    {
        $products = $filterService->filter($request);

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with('attributes')->findOrFail($id);

        return response()->json($product);
    }
}
