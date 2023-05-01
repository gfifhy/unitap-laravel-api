<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SchoolLocation;
use App\Models\SecurityGuard;
use App\Models\Store;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\Violation;
use App\Models\Wallet;
use App\Services\Utils\FileServiceInterface;
use App\Traits\ExceptionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    use ExceptionTrait;
    private $fileService;
    private $studentSignatureFolderName;
    private $studentImageFolderName;
    private $storeFolderName;
    private $productFolderName;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
        $this->studentSignatureFolderName = config('storage.base_path') . 'user_signature';
        $this->studentImageFolderName = config('storage.base_path') . 'user_image';
    }
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

        if(!in_array(explode(';',explode('/',explode(',', $request->user_image)[0])[1])[0], array('jpg','jpeg','png')) ) {
            $this->throwException('student_image has invalid file type', 422);
        }

        if(!in_array(explode(';',explode('/',explode(',', $request->user_signature)[0])[1])[0], array('jpg','jpeg','png')) ) {
            $this->throwException('student_signature has invalid file type', 422);
        }
        if($role->slug === 'admin'){
            $adminFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'user_image' => 'required|string',
                'user_signature' => 'required|string',
            ]);
            $user = User::create([
                'email' => $adminFields['email'],
                'password' => bcrypt($adminFields['password']),
                'nfc_id' => $adminFields['nfc_id'],
                'first_name' => $adminFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $adminFields['last_name'],
                'role_id' => $roleFields['role_id'],
            ]);

            //take image
            $filename = md5($user->id.Carbon::now()->timestamp);
            $user->user_image = $this->fileService->upload($this->studentImageFolderName, $filename, $request->user_image, $user->id);
            $user->user_signature = $this->fileService->upload($this->studentSignatureFolderName, $filename, $request->user_signature, $user->id);
            $user->save();

            return response($user, 201);

        }
        else if($role->slug === 'store'){
            $storeFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'store_name' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'user_image' => 'required|string',
                'user_signature' => 'required|string',
            ]);

            $user = User::create([
                'email' => $storeFields['email'],
                'password' => $storeFields['password'],
                'nfc_id' => $storeFields['nfc_id'],
                'first_name' => $storeFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $storeFields['last_name'],
                'role_id' => $roleFields['role_id'],
            ]);
            $store = Store::create([
                'user_id' => $user->id,
                'store_name' => $storeFields['store_name'],
            ]);
            $result = ['user' => $user, 'store' => $store];

            //take image
            $filename = md5($user->id.Carbon::now()->timestamp);
            $user->user_image = $this->fileService->upload($this->studentImageFolderName, $filename, $request->user_image, $user->id);
            $user->user_signature = $this->fileService->upload($this->studentSignatureFolderName, $filename, $request->user_signature, $user->id);
            $user->save();
            return response($result, 201);

        }
        elseif($role->slug === 'security-guard'){
            $guardFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'user_image' => 'required|string',
                'user_signature' => 'required|string',
            ]);
            $middle_name = (isset($request['middle_name']) ? $request['middle_name'] : null );
            $user = User::create([
                'email' => $guardFields['email'],
                'password' => bcrypt($guardFields['password']),
                'nfc_id' => $guardFields['nfc_id'],
                'first_name' => $guardFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $guardFields['last_name'],
                'role_id' => $roleFields['role_id'],
            ]);

            $guard = SecurityGuard::create([
                'user_id' => $user->id,
                'location_id' => '4b090ffc-41f8-498d-973a-5944f4fdeaad',
            ]);
            $result = ['user' => $user, 'security_guard' => $guard];

            //take image
            $filename = md5($user->id.Carbon::now()->timestamp);
            $user->user_image = $this->fileService->upload($this->studentImageFolderName, $filename, $request->user_image, $user->id);
            $user->user_signature = $this->fileService->upload($this->studentSignatureFolderName, $filename, $request->user_signature, $user->id);
            $user->save();
            return response($result, 201);

        }
        elseif($role->slug === 'guidance-staff'){
            $guidanceFields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8',
                'nfc_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'user_image' => 'required|string',
                'user_signature' => 'required|string',
        ]);
            $user = User::create([
                'email' => $guidanceFields['email'],
                'password' => bcrypt($guidanceFields['password']),
                'nfc_id' => $guidanceFields['nfc_id'],
                'first_name' => $guidanceFields['first_name'],
                'middle_name' => $middle_name,
                'last_name' => $guidanceFields['last_name'],
                'role_id' => $roleFields['role_id'],
            ]);

            //take image
            $filename = md5($user->id.Carbon::now()->timestamp);
            $user->user_image = $this->fileService->upload($this->studentImageFolderName, $filename, $request->user_image, $user->id);
            $user->user_signature = $this->fileService->upload($this->studentSignatureFolderName, $filename, $request->user_signature, $user->id);
            $user->save();
            return response($user, 201);
        }
        else{
            return $this->throwException('Invalid role', 400);
        }


    }
    public function addStudent(Request $request){

        if(!in_array(explode(';',explode('/',explode(',', $request->student_image)[0])[1])[0], array('jpg','jpeg','png')) ) {
            $this->throwException('student_image has invalid file type', 422);
        }

        if(!in_array(explode(';',explode('/',explode(',', $request->student_signature)[0])[1])[0], array('jpg','jpeg','png')) ) {
            $this->throwException('student_signature has invalid file type', 422);
        }
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
            'student_image' => 'required|string',
            'student_signature' => 'required|string',
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
            'location_id' => '231eeaaa-28a5-409e-b1f4-e5c2f27b93fc',
            'contact_number' => $fields['contact'],
            'guardian_id' => $guardian->id,
        ]);



        //take image
        $filename = md5($user->id.Carbon::now()->timestamp);
        $user->user_image = $this->fileService->upload($this->studentImageFolderName, $filename, $request->student_image, $user->id);
        $user->user_signature = $this->fileService->upload($this->studentSignatureFolderName, $filename, $request->student_signature, $user->id);
        $user->save();
        $response =[
            'user' => $user,
            'guardian' => $guardian,
            'student' => $student,
            'wallet' => $wallet,
        ];
        return response($response, 201);
    }


    public function getCountOfStudentPerLocation(){
        $result = [];
        $locations = SchoolLocation::all();

        foreach ($locations as $location1) {
            $result[$location1->location] = count(Student::where('location_id', $location1)->get());
        }

        return $result;
    }

    public function totalViolation() {
        $result = [];
        $violations = Violation::all();
        foreach($violations as $violation) {
            $result[$violation->violation_name] = count(StudentViolation::where('violation_id', $violation->id)->get());
        }
        return $result;
    }
    public function test(Request $request) {
        //return md5("123".Carbon::now()->timestamp);
        //return $this->filzeService->download('develop/develop/student_image/K11831245.jpg', '9904fa5b-53e6-425c-81dc-81dde0503e31');
    }
}
