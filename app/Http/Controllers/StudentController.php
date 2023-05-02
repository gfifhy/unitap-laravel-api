<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Utils\FileServiceInterface;
use App\Traits\ExceptionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    use ExceptionTrait;

    private $fileService;
    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function storeProduct($storeId) {
        $store = Store::where('id', $storeId)->first();
        $user = User::where('id', $store->user_id)->first();
        $products =  Product::whereNull('deleted_at')->where('user_id', $user->id)->get();
        foreach($products as $product){
            $product->image = $this->fileService->download($product->image, $product->user_id);;
        }
        return response($products, 201);
    }
    public function indexStore() {
        $wallet = Wallet::where('user_id', Auth::user()->id)->first();
        if($wallet->isDisabled){
            return $this->throwException('Your wallet is disabled', 402);
        }
        $stores = Store::whereNull('deleted_at')->get();
        foreach($stores as $store){
            $store->store_logo = $this->fileService->download($store->store_logo, $store->user_id);;
        }
        return response($stores, 201);
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
