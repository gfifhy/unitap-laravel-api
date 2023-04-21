<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\ExceptionTrait;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    use ExceptionTrait;
    public function addStaff(Request $request){
        $roleFields = $request->validate([
            'role' => 'required|string',
            'role_id' => 'required|string',
        ]);
        $role = Role::find($roleFields['role_id'])->where('id' , $roleFields['role_id'])->first();
        if(!$role){
            return $this->throwException('Invalid role', 401);
        }
        if($roleFields['role'] === 'admin'){
            $adminFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
            ]);
            $user = User::create([
                'email' => $adminFields['email'],
                'password' => bcrypt($adminFields['password']),
                'nfc_id' => $adminFields['nfc_id'],
            ]);
            return response($user, 201);

        }
        elseif($roleFields['role'] === 'store'){
            $storeFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'store_name' => 'required|string',
            ]);

        }
        elseif($roleFields['role'] === 'security-guard'){

        }
        elseif($roleFields['role'] === 'guidance-staff'){

        }
        else{
            return $this->throwException('Invalid role', 400);
        }


    }
    public function addStudent(Request $request){
        $fields = $request->validate([
            'nfc_id' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'string',
            'last_name' => 'required|string',
            'student_id' => 'required|string|unique:students,student_id',
            'email' => 'required|string|unique:users,email',
            'role_id' => 'required|string',
            'role' => 'required|string',
            'contact' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
            'guardian_first_name' => 'required|string',
            'guardian_middle_name' => 'required|string',
            'guardian_last_name' => 'required|string',
            'guardian_contact' => 'required|string',
        ]);


        $role = Role::where('slug', $fields['role'])->where('id', $fields['role_id'])->first();
        if(!$role){
            return $this->throwException('Invalid Role', 400);
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

        $guardian = StudentGuardian::Create([
            'first_name' => $fields['guardian_first_name'],
            'last_name' => $fields['guardian_last_name'],
            'middle_name' => $fields['guardian_last_name'],
            'contact' => $fields['guardian_contact'],
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
            'student_id' => $fields['student_id'],
            'status' => 'on-premise',
            'contact_number' => $fields['contact'],
            'guardian_id' => $guardian->id,
        ]);
        $response =[
            'user' => $user,
            'guardian' => $guardian,
            'student' => $student,
            'wallet' => $wallet
        ];
        return response($response, 201);
    }
}
