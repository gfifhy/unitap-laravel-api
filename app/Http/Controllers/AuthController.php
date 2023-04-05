<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ExceptionTrait;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
    use ExceptionTrait;
    public function register(Request $request){
        $fields = $request->validate([
            'nfc_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'student_id' => 'required|string',
            'role' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);


        $role = Role::where('slug', $fields['role'])->first();
        if(!$role){
            return $this->throwException('Role Does not exist', 400);
        }

        $user = User::create([
            'role_id' => $role->id,
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'isDisabled' => 0
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'nfc_id' => $fields['nfc_id'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'student_id' => $fields['student_id'],
            'status' => 'on-premise',
        ]);
        $response =[
            'user' => $user,
            'student' => $student,
            'wallet' => $wallet
        ];
        return response($response, 201);
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
        //create token
        $token = $user->createToken('userToken')->plainTextToken;
        $result = [
            'student' => $student,
            'token' => $token
        ];
        return response($result, 201);
    }
    public function profile(Request $request){
        //auth()->user()->role;
        $student = Student::where('user_id', Auth::user()->id)->whereNull('deleted_at')->first();
        $result = [
            'user_data' => $student,
            'user' => Auth::user(),
        ];
        return $result;
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logout'
        ];
    }
}
