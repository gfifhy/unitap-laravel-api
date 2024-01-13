<?php

namespace App\Http\Controllers;

use \DB;
use App\Models\LocationHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\StudentViolation;
use App\Models\Transaction;
use App\Models\Violation;
use App\Traits\ExceptionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AnalyticsController extends Controller
{
    use ExceptionTrait;

    private $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }


    public function getUserRecentStatistics() 
    {

        $user_id = Auth::user()->id;
        
        $startDate = $this->getRelativeTime();

        $labels = [];
        $orders = [];

        for ($curr = $startDate; $curr <= $this->now; $curr->addDay()) {
            $amount_day = 0;
            $all = Order::where('buyer_id', $user_id)
                ->whereDate('created_at', $curr)
                ->get();

            if ($all) {
                foreach ($all as $k => $v) {
                    $product = Product::where('id', $v->product_id)
                        ->firstOrFail();
                    $amount_day += $product->price * $v->quantity;
                }
            }

            array_push($orders, $amount_day);
            array_push($labels, $curr->copy()->locale('en')->shortDayName);
        }

        $startDate = $this->getRelativeTime();
        $transactions = [];

        for ($curr = $startDate; $curr <= $this->now; $curr->addDay()) {
            $amount_day = 0;

            $all = Transaction::where(function ($query) use ($user_id) {
                    $query->where('wallet_id_receiver', $user_id)
                          ->orWhere('wallet_id_sender', $user_id);
                })->whereDate('created_at', $curr)
                ->get();

            if ($all) {
                foreach ($all as $k => $v) {
                    if ($v->wallet_id_receiver == $user_id) {
                        $amount_day += $v->quantity;
                    } else {
                        $amount_day -= $v->quantity;
                    }
                }
            }

            array_push($transactions, $amount_day);
        }

        return response([
            'orders' => $orders,
            'transactions' => $transactions,
            'labels' => $labels,
        ], 200);

    }

    public function violations($time, $id) {

        $this->validateTimeID($time, $id);

        $relativeTime = $this->getRelativeTime($time);
        
        $all = [];
        
        if ($id == '00000000-0000-0000-0000-000000000000') {

            $all = DB::table('student_violations')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereBetween('created_at', [$relativeTime, $this->now])
                ->groupBy('date')
                ->get();

        } else {

            $all = DB::table('student_violations')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereBetween('created_at', [$relativeTime, $this->now])
                ->where('violator_id', $id)
                ->groupBy('date')
                ->get();

        }

        return response($all, 200);
    }

    public function violationsByType($time, $id) {

        $this->validateTimeID($time, $id);

        $relativeTime = $this->getRelativeTime($time);
        
        $queries = [];

        foreach (Violation::all() as $k => $v) {
            $condition = $id !== '00000000-0000-0000-0000-000000000000' ? ['violator_id' => $id] : [];
            $queries[] = DB::table('student_violations')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                ->where($condition)
                ->whereBetween('created_at', [$relativeTime, $this->now])
                ->where('violation_id', $v->id)
                ->groupBy('date')
                ->get();
        }

        $results = [];
        foreach ($queries as $k => $v) {
            $results[] = [
                'data' => $v,
                'violationType' => Violation::pluck('violation_name')[$k]
            ];
        }

        return response($results, 200);
    }

    private function validateTimeID($time, $id) {
        $validator = Validator::make([
            'time' => $time,
            'id' => $id,
        ], [
            'time' => 'required|string|size:1', // week = 'w', month = 'm' year = 'y'
            'id' => 'required|string|size:36',
        ]);

        if (!$validator->passes()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    }
    
    private function getRelativeTime($time = '') {
        switch ($time) {
            case 'm':
                return Carbon::now()->startOfMonth()->subMonth();
            case 'y':       
                return Carbon::now()->startOfYear()->subYear();
            default:
                return Carbon::now()->subWeeks(1);
        }
    }

    private function toISOString($time) {
        $time = Carbon::parse($time);
        $isoString = $time->format('c');
        $jsDateObject = new \DateTime($isoString);
        return $jsDateObject->format('Y-m-d\TH:i:s.uO');
    }
}
