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
    public function addStaff(Request $request){
        $fields = $request->validate([
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'nfc_id' => 'string',
            'role' => 'required|string',
            'role_id' => 'required|string',
        ]);
        if($fields['role'] === 'admin'){
            $user = User::create([
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'nfc_id' => $fields['nfc_id'],
            ]);
            return response($user, 201);

        }
        elseif($fields['role'] === 'store'){

        }
        elseif($fields['role'] === 'security-guard'){

        }
        elseif($fields['role'] === 'guidance-staff'){

        }
        else{
            return $this->throwException('Invalid role', 400);
        }


    }
    public function addStudent(Request $request){
        $fields = $request->validate([
            'nfc_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'student_id' => 'required|string|unique:students,student_id',
            'email' => 'required|string|unique:users,email',
            'role_id' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);


        $role = Role::where('slug', 'student')->first();
        if(!$role){
            return $this->throwException('Role Does not exist', 400);
        }

        $user = User::create([
            'role_id' => $role->id,
            'nfc_id' => $fields['nfc_id'],
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
        //role details
        $role = Role::find($user->role_id);
        //create token
        $token = $user->createToken('token')->plainTextToken;
        $wallet = Wallet::where('user_id', $user->id)->first();
        $result = [
            'user' => $student,
            'role' => $role,
            'wallet' => $wallet,
            'token' => $token
        ];
        return response($result, 201);
    }


    /**
     <h1 style="font-family: Josefin Sans, sans-serif;"> NOTES </h1>
     <p style="font-style: italic;font-family: Josefin Sans, sans-serif;"> Bale pwede ko rin gawing isang function nalang yung pag login ng mga personnel. Gagamit lang ako if else </p>
     */
    public function adminLogin(Request $request){
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

        if($role->slug == 'admin') {
            $user = User::where('email', $fields['email'])->first();
            if(!$user) {
                return $this->throwException('Email Does Not Exist', 400);
            }
            if(!Hash::check($fields['password'], $user->password)) {
                return $this->throwException('Wrong Password!', 400);
            }
            $role = Role::find($user->role_id);
            $token = $user->createToken('token')->plainTextToken;

            $result = [
                'user' => $user,
                'role' => $role,
                'token' => $token,
            ];
            return $result;
        }
        else {
            return $this->throwException('Invalid role', 400);
        }



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