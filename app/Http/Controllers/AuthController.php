<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SchoolLocation;
use App\Models\SecurityGuard;
use App\Models\Store;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ExceptionTrait;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ExceptionTrait;
    public function studentLogin(Request $request){
        $fields = $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string'
        ]);
        //get student data
        $student = Student::where('student_id', $fields['student_id'])
                            ->whereNull('deleted_at')
                            ->first();

        //check if the query have data
        if(!$student){
            //return response('Student id number does not exist', '400');
            return  $this->throwException('Student id number does not exist', '400');
        }

        //get student's data from users table
        $user = User::where('id', $student->user_id)->first();
        //check if password is correct
        if(!Hash::check($fields['password'], $user->password)){
            return  $this->throwException('Wrong password', '400');
        }
        //role details
        $role = Role::find($user->role_id);
        //create token
        $token = $user->createToken('token', Carbon::now()->addDays(3))->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)->first();
        $result = [
            'user' => $student,
            'role' => $role,
            'wallet' => $wallet,
            'token' => $token
        ];
        $cookie = cookie('auth_token', $token, strtotime('2 days'), null, null, false);
        return response($result, 201)->withCookie($cookie);
    }


    /**
     <h1 style="font-family: Josefin Sans, sans-serif;"> NOTES </h1>
     <p style="font-style: italic;font-family: Josefin Sans, sans-serif;"> Bale pwede ko rin gawing isang function nalang yung pag login ng mga personnel. Gagamit lang ako if else </p>
     */
    public function adminLogin(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'role_id' => 'required|string',
        ]);

        //ito magiging role nalang since general na nga.
        $role = Role::where('id', $fields['role_id'])->first();
        if(!$role){
            return $this->throwException('Invalid role id', 400);
        }
        $user = User::where('email', $fields['email'])->first();
        if(!$user) {
            return $this->throwException('Email Does Not Exist', 400);
        }
        if(!Hash::check($fields['password'], $user->password)) {
            return $this->throwException('Wrong Password!', 400);
        }
        $role = Role::find($user->role_id);
        $token = $user->createToken('token', Carbon::now()->addDays(3))->plainTextToken;
        if($role->slug == 'admin') {
            $result = [
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ];
            return response($result, 201);
        }
        else {
            return $this->throwException('Invalid role', 400);
        }
    }

    public function staffLogin(Request $request){
        //bale labas ng if else to
        $fields = $request->validate([
           'email' => 'required|string',
           'password' => 'required|string',
            'role_id' => 'required|string',
        ]);

        //ito magiging role nalang since general na nga.
        $role = Role::where('id', $fields['role_id'])->first();
        if(!$role){
            return $this->throwException('Invalid role id', 400);
        }
        $user = User::where('email', $fields['email'])->first();
        if(!$user) {
            return $this->throwException('Email Does Not Exist', 400);
        }
        if(!Hash::check($fields['password'], $user->password)) {
            return $this->throwException('Wrong Password!', 400);
        }
        $role = Role::find($user->role_id);
        $token = $user->createToken('token', Carbon::now()->addDays(3))->plainTextToken;

        if($role->slug == 'store') {
            $storeInfo = Store::where('user_id', $user->id)->first();
            return response([
                'information' => $storeInfo,
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ], 201);
        }
        else if ($role->slug == 'security-guard') {
            $guardInfo = SecurityGuard::where('user_id', $user->id)->first();
            $guardInfo->location = SchoolLocation::where('id', $guardInfo->location_id)->first();
            $result =  response([
                'information' => $guardInfo,
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ], 201);
            Auth()->guard_information = $result;

            return $result;
        }
        else if ($role->slug == 'guidance-staff') {
            return response([
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ], 201);
        }
        else {
            return $this->throwException('Invalid role', 400);
        }
    }
    public function profile(Request $request){
        //auth()->user()->role;
        if(Auth::user()->role->slug == 'student'){
            $user_data = Student::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();
        }
        else if(Auth::user()->role->slug == 'store'){
            $user_data = Store::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();
        }
        else if(Auth::user()->role->slug == 'security-guard'){
            $user_data = SecurityGuard::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();
        }
        else {
            $user_data = "";
        }

        $result = [
            'user_data' => $user_data,
            'user' => auth()->user(),
        ];
        return response($result, 201)->withCookie(cookie('user_id', Auth::user()->id, $minutes = 60, null, null, true, true));
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logout'
        ];
    }
}
