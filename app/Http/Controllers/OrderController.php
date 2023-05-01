<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request){
        $fields = $request->validate([
            'product_id' => 'string|required',
            'quantity' => 'required|string'
        ]);
        $product = Product::where('id', $fields['product_id'])->first();

    }
}
