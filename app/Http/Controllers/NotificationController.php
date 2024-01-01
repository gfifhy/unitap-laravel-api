<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;
use App\Services\Utils\FileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{

    private $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
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

    public function mark($id) // user notif
    {
        $current_user = Auth::user();
        $target = Notification::withoutTrashed()
            ->where('id', $id)
            ->where('for_id', $current_user->id)
            ->first();
        $target->is_read = Carbon::now();
        $target->save();
    }

    public function markAll() // user notif
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
        }

        $all->each(function ($v) {
            $v->is_received = now();
            $v->save();
        });

        return response($result, 200);

    }

    public function destroy($id)
    {
        return response(Notification::where('id', $id)->first()->delete());
    }

    public function store(Request $request)
    {
        $rules = [
            'target' => 'string|required',
            'type' => 'string|required',
            'event' => 'string|required',
            'description' => 'string|required',
            'pushDate' => 'string|required',
        ];

        $req = [];

        if (isset($request["img"])) {

            $req = $request->validate(array_merge($rules, 
                ['img' => 'image|mimes:png,jpeg,jpg,webp|max:2048']
            ));
            
            $img = $req['img'];

            if (get_class($img) === 'Illuminate\Http\UploadedFile') {
                $img_pth = $img->storeAs('public', 
                    uniqid() . '_' . $img->getClientOriginalName()
                );
            } 

            $req["img"] = Storage::url($img_pth);
            
        } else {
            $req = $request->validate($rules);
            $req["img"] = null;
        }

        $current_user = Auth::user()->id;

        $insert_rule = [
            'type' => $req['type'],
            'event' => $req['event'],
            'description' => $req['description'],
            'img' => $req["img"],
            'push_date' => Carbon::createFromFormat('Y-m-d\TH:i:s.uP',
                $req['pushDate'], 'UTC')->timezone('Asia/Manila')
        ];

        foreach (explode(',', $req['target']) as $v) {

            if (User::where('id', $v)->first()) {

                $data = new Notification($insert_rule);
                $data->from_id = $current_user;
                $data->for_id = $v;
                $data->save();

            } elseif ($v == 'all') {

                $data = new Notification($insert_rule);
                $data->from_id = $current_user;
                $data->for_id = null;
                $data->save();
                
            } else {

                return response([
                    'message' => 'Invalid target data.'
                ], 422);

            }
            
        }


        return response()->noContent();
    }

    private function toISOString($time) {
        $time = Carbon::parse($time);
        $isoString = $time->format('c');
        $jsDateObject = new \DateTime($isoString);
        return $jsDateObject->format('Y-m-d\TH:i:s.uO');
    }
}
