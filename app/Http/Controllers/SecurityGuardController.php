<?php

namespace App\Http\Controllers;

use App\Models\LocationHistory;
use App\Models\SchoolLocation;
use App\Models\SecurityGuard;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\Violation;
use App\Services\NotificationService;
use App\Services\Utils\FileServiceInterface;
use App\Traits\ExceptionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Guard;

class SecurityGuardController extends Controller
{
    use ExceptionTrait;

    private $fileService;
    protected $notificationService;

    public function __construct(
        FileServiceInterface $fileService,
        NotificationService $notificationService
    )
    {
        $this->fileService = $fileService;
        $this->notificationService = $notificationService;
    }

    public function studentEntry(Request $request) {
        $fields = $request->validate([
            'user_id' => 'required|string',
            'nfc_id' => 'required|string',
        ]);
        $studentUser = User::where('id', $fields['user_id'])->where('nfc_id', $fields['nfc_id'])->with('role')->first();

        if(!$studentUser) {
            return $this->throwException("NFC ID Mismatch. Please contact the IT Support", 401);
        }
        $student = Student::where('user_id', $studentUser->id)->first();
        $guard = SecurityGuard::where('user_id', auth()->user()->id)->with('location')->first();
        $student->location_id = $guard->location_id;
        $student->save();

        LocationHistory::create([
            'location_id' => $guard->location_id,
            'user_id' => $student->id,
        ]);

        $violation = StudentViolation::where('status', 'violated')->where('violator_id', $studentUser->id)->get();
        $studentUser->user_image = $this->fileService->download($studentUser->user_image, $studentUser->id);
        $studentUser->user_signature = $this->fileService->download($studentUser->user_signature, $studentUser->id);
        return response(['security-guard' => $guard, 'student' => $student, 'student_user' => $studentUser, 'violations' => $violation], 200);
    }

    public function violationList(){
        $all = Violation::all();

        $response = [];

        foreach ($all as $k => $v) {
            array_push($response, [
                'id' => $v['id'],
                'label' => $v['violation_name'],
                'icon' => $v['icon'] ?? ''
            ]);
        }

        return response($response, 200);
    }

    public function userViolationList() {

        $all = StudentViolation::all();

        $result = [];
        
        foreach ($all as $k => $v) {

            $violation = Violation::where('id', $v['violation_id'])
                ->first()->violation_name;

            $violator = User::where('id', $v['violator_id'])->first();

            array_push($result, [
                'violation' => $violation,
                'violator' => $violator['first_name'] . ' ' . $violator['last_name'],
                'date' => $v->updated_at,
                'status' => $v->status,
                'note' => $v->note,
            ]);
            
        }

        return response($result, 200);
    }

    public function addViolation(Request $request) {

        $fields = $request->validate([
            'violation_id' => 'required|string',
            'violator_id' => 'required|string',
            //'guard_id' => 'required|string',
            'note' => 'string|nullable',
        ]);

        $violation = [];

        foreach (explode(',', $fields['violator_id']) as $v) {
            array_push($violation, StudentViolation::create([
                'violation_id' => $fields['violation_id'],
                'violator_id' => $v,
                //'guard_id' => $fields['guard_id'],
                'guard_id' => SecurityGuard::where('user_id', Auth::user()->id)
                    ->first()->id,
                'status' => 'violated',
                'note' => $fields['note'] ?? '',
            ]));
        }

        $notif = $this->notificationService->createNotification([
            'target' => $fields['violator_id'],
            'type' => 'warn',
            'event' => 'Violation: ' . 
                Violation::where('id', $fields['violation_id'])
                ->first()->violation_name,
            'description' => 'You have received an offense record.',
            'pushDate' => $this->toISOString(Carbon::now()),
        ], false);
        
        if ($notif) {
            return response($notif[0], $notif[1]);
        } else {
            return response($violation, 200);
        }


    }

    public function update(Request $request) {
        $fields = $request->validate([
            'location_id' => 'required|string'
        ]);
        $securityGuard = SecurityGuard::where('user_id', Auth::user()->id)->first();
        $securityGuard->location_id = $fields['location_id'];
        $securityGuard->save();
        return $securityGuard;
    }

    public function location_index() {
        
        $all = SchoolLocation::all();

        $response = [];

        foreach ($all as $k => $v) {
            array_push($response, [
                'id' => $v['id'],
                'label' => $v['location'],
                'icon' => $v['icon'] ?? ''
            ]);
        }

        return response($response, 200);

    }

    private function toISOString($time) {
        $time = Carbon::parse($time);
        $isoString = $time->format('c');
        $jsDateObject = new \DateTime($isoString);
        return $jsDateObject->format('Y-m-d\TH:i:s.uO');
    }
}
