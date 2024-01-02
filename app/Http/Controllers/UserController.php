<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Role;
use App\Models\SecurityGuard;
use App\Models\Student;
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
            $user->role = Role::find($user->role_id);
            return $user;
        });
        return $users;
    }
    
    public function guard_index()
    {

        $users = User::whereNull('deleted_at')
            ->where('role_id', Role::where('slug', 'student')->first()->id)
            ->orderBy('role_id')->paginate(50);
        $users->data = $users->map(function ($user) {
            $user->user_image = null;
            $user->user_signature = null;
            $user->role = [ 'name' => 'Student'];
            $user->email = '****';
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
        foreach ($request->input() as $req){
            if($req == null){
                return response(['message'=>'request must not be null'], 422);
            }
        }
        $user = User::where('id', $id)->with('role')->first();
        $user->update($request->all());
        if($user->role->slug === 'student'){
            $user['student'] = Student::where('user_id', $user->id)->first();
            $user['student']->update($request->only(['contact_number', 'student_id', 'location_id']));
            $user['student']['wallet'] = Wallet::where('user_id', $user->id)->first();
            $user['student']['wallet']->update($request->only(['balance', 'isDisabled']));
        }
        else if($user->role->slug === 'security-guard'){
            $user['guard'] = SecurityGuard::where('user_id', $user->id)->first();
            $user['guard']->update($request->only(['location_id']));
        }
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        if(Role::find($user->role_id)->slug === "student"){
            Wallet::where('user_id', $user->id)->update(['deleted_at' => now()]);
            StudentGuardian::where('id', Student::where('user_id', $user->id)->first()->guardian_id)->update(['deleted_at' => now()]);
        }
        if(Role::find($user->role_id)->slug === "store"){
            Wallet::where('user_id', $user->id)->update(['deleted_at' => now()]);
            $product = Product::where('user_id', $user->id)->get();
            for($i=0; $i<count($product); $i++) {
                $product[$i]->deleted_at = Carbon::now();
                $product[$i]->save();
            }
        }
        return response($user, 201);
    }
}
