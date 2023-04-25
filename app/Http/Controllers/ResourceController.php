<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SecurityGuard;
use App\Models\Store;
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
        $middle_name = (isset($request['middle_name']) ? $request['middle_name'] : null );
        $role = Role::find($roleFields['role_id'])->where('id' , $roleFields['role_id'])->first();
        if(!$role){
            return $this->throwException('Invalid role', 401);
        }
        if($roleFields['role'] === 'admin'){
            $adminFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);
            $user = User::create([
                'email' => $adminFields['email'],
                'password' => bcrypt($adminFields['password']),
                'nfc_id' => $adminFields['nfc_id'],
                'first_name' => $adminFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $adminFields['last_name'],
            ]);
            return response($user, 201);

        }
        elseif($roleFields['role'] === 'store'){
            $storeFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'store_name' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);

            $user = User::create([
                'email' => $storeFields['email'],
                'password' => $storeFields['password'],
                'nfc_id' => $storeFields['nfc_id'],
                'first_name' => $storeFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $storeFields['last_name'],
            ]);
            $store = Store::create([
                'user_id' => $user->id,
                'store_name' => $storeFields['store_name'],
            ]);
            $result = ['user' => $user, 'store' => $store];
            return response($result, 201);

        }
        elseif($roleFields['role'] === 'security-guard'){
            $guardFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
            ]);
            $middle_name = (isset($request['middle_name']) ? $request['middle_name'] : null );
            $user = User::create([
                'email' => $guardFields['email'],
                'password' => $guardFields['password'],
                'nfc_id' => $guardFields['nfc_id'],
                'first_name' => $guardFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $guardFields['last_name'],
            ]);
            $guard = SecurityGuard::create([
                'user_id' => $user->id,
                'location_id' => '4b090ffc-41f8-498d-973a-5944f4fdeaad',
            ]);
            $result = ['user' => $user, 'security_guard' => $guard];
            return response($result, 201);

        }
        elseif($roleFields['role'] === 'guidance-staff'){
            $guidanceFields = $request->validate([
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8',
            'nfc_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);
            $user = User::create([
                'email' => $guidanceFields['email'],
                'password' => bcrypt($guidanceFields['password']),
                'nfc_id' => $guidanceFields['nfc_id'],
                'first_name' => $guidanceFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $guidanceFields['last_name'],
            ]);
            return response($user, 201);
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
            'student_image' => 'required|image',
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
            'first_name' => $fields['first_name'],
            'middle_name' => $fields['middle_name'],
            'last_name' => $fields['last_name'],
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
