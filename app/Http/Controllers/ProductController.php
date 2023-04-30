<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Product::whereNull('deleted_at')->where('user_id', auth()->user()->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'product_name' => 'required|string',
            'user_id' => 'string',
            'description' => 'string',
            'image' => 'required|string',
            'stock' => 'required|string',
            'price' => 'required|string',
        ]);

        $product = Product::create([
            'product_name' => $fields['product_name'],
            'user_id' => Auth::user()->id,
            'description' => $fields['description'],
            'image' => $fields['image'],
            'stock' => $fields['stock'],
            'price' => $fields['price'],
        ]);

        return  response($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::where('user_id',Auth::user()->id)->where('product_id', $id)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
