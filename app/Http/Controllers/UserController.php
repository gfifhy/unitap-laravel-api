<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Role;
use App\Models\StudentGuardian;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Utils\FileServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::whereNull('deleted_at')->orderBy('role_id')->paginate(15);
        $users->data = $users->map(function ($user) {
            $user->user_image = $this->fileService->download($user->user_image, $user->id);
            $user->user_signature = $this->fileService->download($user->user_signature, $user->id);
            return $user;
        });
        return $users;
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
        $user = User::where('id', $id)->first();
        $user->user_image = $this->fileService->download($user->user_image, $user->id);
        $user->user_signature = $this->fileService->download($user->user_signature, $user->id);
        return $user;
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
        $flight = User::find($id);

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
