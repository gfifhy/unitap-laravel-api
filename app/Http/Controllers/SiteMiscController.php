<?php

namespace App\Http\Controllers;

use App\Models\CMSLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiteMiscController extends Controller
{

    public function getLogoText(Request $request)
    {
        $text = CMSLanding::where('type', 'logotext')->first();
        if ($text){
            return $text->value;
        } else {
            return false;
        }
    }
    public function setPictures(Request $request)
    {
        if (isset($request["login"]) && $request["login"] != null) {

            $req = $request->validate([
                'login' => 'image|mimes:png,jpeg,jpg,webp,gif,ico|max:2048',
            ]);

            $req['login']->storeAs('public', 'login');
 
        }

        if (isset($request["logo"]) && $request["logo"] != null) {

            $req = $request->validate([
                'logo' => 'image|mimes:png,jpeg,jpg,webp,gif,ico|max:2048',
            ]);
            
            $req['logo']->storeAs('public', 'logo');
 
        }

        if (isset($request["text"]) && $request["text"] != null) {

            $req = $request->validate([
                'text' => 'json',
            ]);
            
            CMSLanding::updateOrCreate(
                ['type' => 'logotext'],
                ['value' => $req['text']]
            );
 
        }

        return response(200);
    }


}
