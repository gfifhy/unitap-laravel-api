<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SchoolLocation;
use App\Models\SecurityGuard;
use App\Models\Store;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Utils\FileServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ExceptionTrait;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ExceptionTrait;

    private $fileService;
    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
    }
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
        ];

        $cookie = cookie('auth_token', $token, 60*24*3, '/', null, true, true, false, 'None');
        return response($result, 201)->withCookie($cookie);
    }
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
            ];
            $cookie = cookie('auth_token', $token, 60*24*3, '/', null, true, true, false, 'None');
            return response($result, 201)->withCookie($cookie);
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
            $result = [
                'information' => $storeInfo,
                'user' => $user,
                'role' => $role,
            ];
        }
        else if ($role->slug == 'security-guard') {
            $guardInfo = SecurityGuard::where('user_id', $user->id)->first();
            $guardInfo->location = SchoolLocation::where('id', $guardInfo->location_id)->first();
            $result = [
                'information' => $guardInfo,
                'user' => $user,
                'role' => $role,
            ];
            Auth()->guard_information = $result;

            $cookie = cookie('auth_token', $token, 60*24*3, '/', null, true, true, false, 'None');
            return response($result, 201)->withCookie($cookie);
        }
        else if ($role->slug == 'guidance-staff') {
            $result = [
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ];
        }
        else {
            return $this->throwException('Invalid role', 400);
        }

        $cookie = cookie('auth_token', $token, 60*24*3, '/', null, true, true, false, 'None');
        return response($result, 201)->withCookie($cookie);
    }
    public function profile(Request $request){
        //auth()->user()->role;
        $user_data = array();
        $user_info = User::where('id', Auth::user()->id)->first();
        $user_info->user_image = $this->fileService->download($user_info->user_image, Auth::user()->id);
        $user_info->user_signature = $this->fileService->download($user_info->user_signature, Auth::user()->id);
        if(Auth::user()->role->slug == 'student'){
            $user_data['student'] = Student::where('user_id', Auth::user()->id)->first();
            $user_data['wallet'] = Wallet::where('user_id', Auth::user()->id)->first();
            $user_data['transactions']['sent'] = Transaction::where('wallet_id_sender', Auth::user()->id)->first();
            $user_data['transactions']['received'] = Transaction::where('wallet_id_receiver', Auth::user()->id)->first();
            $user_data['violations'] = StudentViolation::where('violator_id', Auth::user()->id)->first();
        }
        else if(Auth::user()->role->slug == 'store'){
            $user_data['store'] = Store::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();$user_data['wallet'] = Wallet::where('user_id', Auth::user()->id)->first();
            $user_data['transactions']['sent'] = Transaction::where('wallet_id_sender', Auth::user()->id)->first();
            $user_data['transactions']['received'] = Transaction::where('wallet_id_receiver', Auth::user()->id)->first();
        }
        else if(Auth::user()->role->slug == 'security-guard'){
            $user_data = SecurityGuard::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();
        }
        else {
            $user_data = "";
        }

        $result = [
            'user_data' => $user_data,
            'user' => $user_info,
        ];
        return response($result, 201);
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logout'
        ];
    }
}
