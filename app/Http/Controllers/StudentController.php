<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\ExceptionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    use ExceptionTrait;

    public function storeProduct($storeId) {
        $store = Store::where('store_id', $storeId)->first();
        $user = User::where('id', $store->user_id)->first();
        return Product::whereNull('deleted_at')->where('user_id', $user->id);
    }
    public function indexStore() {
        $wallet = Wallet::where('user_id', Auth::user()->id)->first();
        if($wallet->isDisabled){
            return $this->throwException('Your wallet is disabled', 402);
        }
        return Store::whereNull('deleted_at')->get();
    }
    public function order(Request $request) {
        $fields = $request->validate([
            'product_id' => "required|string",
            'quantity' => "required|string",
        ]);
        $product = Product::where('id', $fields['product_id'])->first();
        $user = User::where('id', $product->user_id)->first();
        $order = Order::create([
            'product_id' => $fields['product_id'],
            'buyer_id' => Auth::user()->id,
            'seller_id' => $user->id,
            'status' => 'processing',
        ]);

        return response($order, 201);

    }
}
