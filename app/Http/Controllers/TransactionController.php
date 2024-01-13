<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Services\Utils\FileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index() {
        return Transaction::all();
    }

    public function getUserTransfers() 
    {
        $user = User::where('id', Auth::user()->id)->firstOrFail();

        $all = Transaction::where(function ($query) use ($user) {
            $query->where('wallet_id_receiver', $user->id)
                  ->orWhere('wallet_id_sender', $user->id);
        })->latest()->get();

        $result = [];

        foreach ($all as $k => $v) {

            $agent = null;
            $type = 'receive';

            if ($v->wallet_id_receiver == $user->id) {
                $agent = User::where('id', $v->wallet_id_sender)->firstOrFail();
            } else {
                $agent = User::where('id', $v->wallet_id_receiver)->firstOrFail();
                $type = 'send';
            }

            $avatar = $this->fileService
                ->download($agent->user_image, $agent->id);

            array_push($result, [
                'date' => $v->created_at,
                'type' => $type,
                'agent' => $agent->first_name . ' ' . $agent->last_name,
                'amount' => $v->quantity,
                'avatar' => $avatar,
            ]);

        }

        return response($result, 200);
    }

    public function getRecentTransactions() 
    {
        $user = User::where('id', Auth::user()->id)->firstOrFail();

        $all = Transaction::where(function ($query) use ($user) {
            $query->where('wallet_id_receiver', $user->id)
                  ->orWhere('wallet_id_sender', $user->id);
        })->latest()->limit(3)->get();

        $result = [];

        foreach ($all as $k => $v) {

            $agent = null;
            $type = 'receive';

            if ($v->wallet_id_receiver == $user->id) {
                $agent = User::where('id', $v->wallet_id_sender)->firstOrFail();
            } else {
                $agent = User::where('id', $v->wallet_id_receiver)->firstOrFail();
                $type = 'send';
            }

            $avatar = $this->fileService
                ->download($agent->user_image, $agent->id);

            array_push($result, [
                'date' => $v->created_at,
                'type' => $type,
                'agent' => $agent->first_name . ' ' . $agent->last_name,
                'amount' => $v->quantity,
                'avatar' => $avatar,
            ]);

        }

        $all = Order::where('buyer_id', Auth::user()->id)
            ->latest()->limit(3)->get();

        foreach ($all as $k => $v) {

            $product = Product::where('id', $v->product_id)->firstOrFail();
            $seller = Store::where('user_id', $v->seller_id)->firstOrFail();
            $avatar = $this->fileService
                ->download($seller->store_logo, $seller->id);
            array_push($result, [
                'date' => $v->created_at,
                'type' => 'order',
                'agent' => $seller->store_name,
                'amount' => $product->price * $v->quantity,
                'avatar' => $avatar,
            ]);

        }
        usort($result, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return response($result, 200);
    }
}
