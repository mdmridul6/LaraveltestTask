<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProduct(Request $request)
    {
        // return response()->json($request->search);
        $products = Product::where('name', 'like', '%' . $request->search . '%')->get();
        return response()->json($products);
    }
}
