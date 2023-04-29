<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return Product::whereNull('deleted_at')->where('user_id', auth()->user()->id);
    }


}
