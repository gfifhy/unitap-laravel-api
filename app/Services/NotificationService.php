<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationService
{
    public function createNotification($request, $validate = true)
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

            if ($validate) {
                $req = $request->validate(array_merge($rules, 
                    ['img' => 'image|mimes:png,jpeg,jpg,webp|max:2048']
                ));
            } else {
                $req = $request;
            }
            
            $img = $req['img'];

            if (get_class($img) === 'Illuminate\Http\UploadedFile') {
                $img_pth = $img->storeAs('public', 
                    uniqid() . '_' . $img->getClientOriginalName()
                );
            } 

            $req["img"] = Storage::url($img_pth);
            
        } else {
            if ($validate) {
                $req = $request->validate($rules);
            } else {
                $req = $request;
            }
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

                return [['message' => 'Invalid target data.'], 422];

            }
            
        }

        return [[], 200];
    }
}