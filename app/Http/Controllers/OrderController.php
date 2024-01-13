<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Store;
use App\Models\Wallet;
use App\Services\Utils\FileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index()
    {
        return Transaction::all();
    }

    public function getUserOrders(){

        $all = Order::where('buyer_id', Auth::user()->id)->latest()->get();

        $result = [];

        foreach ($all as $k => $v) {

            $product = Product::where('id', $v->product_id)->firstOrFail();
            $seller = Store::where('user_id', $v->seller_id)->firstOrFail();
            $avatar = $this->fileService
                ->download($seller->store_logo, $seller->id);
            array_push($result, [
                'id' => $v->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $v->quantity,
                ],
                'seller' => [
                    'store_name' => $seller->store_name,
                    'store_logo' => $avatar,
                ],
                'status' => $v->status,
                'date' => $v->created_at
            ]);

        }

        return response($result, 200);
                
    }

    public function getStoreOrders(){

        $all = Order::where('seller_id', Auth::user()->id)->latest()->get();

        $result = [];

        foreach ($all as $k => $v) {

            $product = Product::where('id', $v->product_id)->firstOrFail();
            $buyer = User::where('id', $v->buyer_id)->firstOrFail();
            $avatar = $this->fileService
                ->download($buyer->user_image, $buyer->id);
            array_push($result, [
                'id' => $v->id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'price' => $product->price,
                    'quantity' => $v->quantity,
                ],
                'buyer' => [
                    'first_name' => $buyer->first_name,
                    'last_name' => $buyer->last_name,
                    'user_image' => $avatar,
                ],
                'status' => $v->status,
                'date' => $v->created_at
            ]);

        }

        return response($result, 200);
                
    }

    public function orderProduct(Request $request){

        $fields = $request->validate([
            'product_id' => 'string|required|size:36',
            'quantity' => 'required|numeric'
        ]);

        $product = Product::where('id', $fields['product_id'])->firstOrFail();
        $quantity = $fields['quantity'];
        $stock = $product['stock'];

        if (!($quantity >= 1 &&
            $quantity <= $stock)) {
            return response([
                'message' => ['Selected quantity out of range.']
            ], 422);
        }

        $user = Auth::user()->id;

        $user_wallet = Wallet::where('user_id', $user)->firstOrFail();

        $cost = $quantity * $product->price;

        if (!($cost <= $user_wallet->balance)) {
            return response([
                'message' => ['Wallet balance not enough.']
            ], 422);
        }
        
        $seller = User::where('id', $product->user_id)->firstOrFail();

        $res = Order::create([
            'product_id' => $product->id,
            'buyer_id' => $user,
            'seller_id' => $seller->id,
            'quantity' => $quantity,
            'status' => 'pending'
        ]);

        if ($res) {
            $product->stock -= $quantity;
            $product->save();
            $user_wallet->balance -= $cost;
            $user_wallet->save();
            return response(array_merge($res->toArray(), [
                'balance' => $user_wallet->balance
            ]), 200);
        }


    }

    public function cancelOrder(Request $request){

        $fields = $request->validate([
            'order_id' => 'string|required|size:36',
        ]);

        $order = Order::where('id', $fields['order_id'])->firstOrFail();

        $seller = User::where('id', $order->seller_id)->firstOrFail();

        if (!($seller->id == Auth::user()->id)) {
            return response([
                'message' => ["Unauthorized seller of order."]
            ], 401);
        }

        if ($order->status == 'pending') {
            $order->status = 'canceled';
            $order->save();
        } else {
            return response([
                'message' => ["Order is $order->status."]
            ], 422);
        }


    }

    public function fulfillOrder(Request $request){

        $fields = $request->validate([
            'order_id' => 'string|required|size:36',
        ]);

        $order = Order::where('id', $fields['order_id'])->firstOrFail();

        $seller = User::where('id', $order->seller_id)->firstOrFail();

        if (!($seller->id == Auth::user()->id)) {
            return response([
                'message' => ["Unauthorized seller of order."]
            ], 401);
        }

        if ($order->status == 'pending') {
            $product = Product::where('id', $order->product_id)->firstOrFail();
            $store_wallet = Wallet::where('user_id', $seller->id)->firstOrFail();
            $store_wallet->balance += $product->price * $order->quantity;
            $store_wallet->save();
            $order->status = 'fulfilled';
            $order->save();
            $buyer = User::where('id', $order->buyer_id)->firstOrFail();
            return response([$buyer->first_name, $buyer->last_name], 200);
        } else {
            return response([
                'message' => ["Order is $order->status."]
            ], 422);
        }


    }
}
