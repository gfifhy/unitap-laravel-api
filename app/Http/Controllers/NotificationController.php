<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Utils\FileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    private $fileService;
    protected $notificationService;

    public function __construct(
        FileServiceInterface $fileService,
        NotificationService $notificationService
    )
    {
        $this->fileService = $fileService;
        $this->notificationService = $notificationService;
    }

    public function _all()
    {
        $all = Notification::withoutTrashed()->get();

        $result = [];

        foreach ($all as $k => $v) {
            $agent = User::where('id', $v['from_id'])->first();
            $target = User::where('id', $v['for_id'])->first();
            $avatar = null;
            if ($target) {
                $avatar = $this->fileService->download($target->user_image, $target->id);
            }
            array_push($result, [
                'id' => $v['id'],
                'avatar' => $avatar,
                'agent' => [
                    'id' => $agent['id'],
                    'first_name' => $agent['first_name'],
                    'last_name' => $agent['last_name']
                ],
                'target' => [
                    'id' => $target['id'] ?? null,
                    'first_name' => $target['first_name'] ?? null,
                    'last_name' => $target['last_name'] ?? null
                ],
                'type' => $v['type'],
                'event' => $v['event'],
                'pushDate' => $this->toISOString($v['push_date']),
                'img' => $v['img'],
                'dateCreated' => $this->toISOString($v['created_at'])
            ]);
        }

        return response($result, 200);
    }

    public function mark($id) // user mark as read
    {
        $current_user = Auth::user();
        $target = Notification::withoutTrashed()
            ->where('id', $id)
            ->where('for_id', $current_user->id)
            ->first();
        $target->is_read = Carbon::now();
        $target->save();
    }

    public function markAll()
    {

        $current_user = Auth::user();

        $all = Notification::withoutTrashed()
            ->where('for_id', $current_user->id)
            ->get();

        $all->each(function ($v) {
            $v->is_read = Carbon::now();
            $v->save();
        });

        return response()->noContent();
    }

    public function index(Request $request) // user notif
    {

        $current_user = Auth::user();

        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth()->subMonth();

        $all = Notification::withoutTrashed()
            ->where(function ($query) use ($current_user) {
                $query->where('for_id', $current_user->id)
                      ->orWhereNull('for_id');
            })
            ->whereBetween('push_date', [$startOfMonth, $now])
            ->latest()
            ->get();

        $result = [];

        foreach ($all as $k => $v) {

            $agent = User::where('id', $v['from_id'])->first();
            $avatar = $this->fileService
                ->download($agent->user_image, $agent->id);

            $queue = [
                'id' => $v['id'],
                'avatar' => $avatar,
                'agent' => [
                    'first_name' => $agent['first_name'],
                    'last_name' => $agent['last_name']
                ],
                'type' => $v['type'],
                'event' => $v['event'],
                'description' => $v['description'],
                'pushDate' => $this->toISOString($v['push_date']),
                'img' => $v['img'],
                'isRead' => $v['is_read'],
            ];

            if ($v['for_id'] === null) {
                $queue['all'] = true;
                $queue['isReceived'] = $this->toISOString($v['is_received']);
            }
/*
            if ($agent['role']['slug'] != 'student') {
                $queue['agent'] = [
                    //'id' => $agent['id'],
                    'first_name' => $agent['first_name'],
                    'last_name' => $agent['last_name']
                ];
            }
*/
            array_push($result, $queue);
            $v->is_received = Carbon::now();
            $v->save();
        }

        return response($result, 200);

    }

    public function destroy($id)
    {
        return response(Notification::where('id', $id)->first()->delete());
    }

    public function store(Request $request)
    {

        $res = $this->notificationService->createNotification($request);
        
        if ($res) {
            return response($res[0], $res[1]);
        } else {

            return response()->noContent();
        }

    }

    private function toISOString($time) {
        $time = Carbon::parse($time);
        $isoString = $time->format('c');
        $jsDateObject = new \DateTime($isoString);
        return $jsDateObject->format('Y-m-d\TH:i:s.uO');
    }
}
