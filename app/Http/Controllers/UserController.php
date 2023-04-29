<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Role;
use App\Models\StudentGuardian;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::whereNull('deleted_at')->orderBy('role_id')->paginate(2);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::where('id', $id)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function destroy($id)
    {
        $user =  User::find($id);
        $user->deleted_at = Carbon::now();
        $user->save();
        if(Role::find($user->role_id)->slug === "student"){
            $wallet = Wallet::where('user_id', $user->id);
            $wallet->deleted_at = Carbon::now();
            $wallet->save();
            $studentGuardian = StudentGuardian::where('user_id', $user->id);
            $studentGuardian->deleted_at = Carbon::now();
            $studentGuardian->save();
        }
        if(Role::find($user->role_id)->slug === "store"){
            $wallet = Wallet::where('user_id', $user->id);
            $wallet->deleted_at = Carbon::now();
            $wallet->save();
            $product = Product::where('user_id', $user->id)->get();
            for($i=0; $i<count($product); $i++) {
                $product[$i]->deleted_at = Carbon::now();
                $product[$i]->save();
            }
        }
        return response($user, 201);
    }
}
