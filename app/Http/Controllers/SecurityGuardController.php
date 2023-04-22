<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityGuardController extends Controller
{
    public function studentEntry(Request $request) {
        $fields = $request->validate([
            'id' => 'required|string',
            'nfc_id' => 'required|string',
            'student_id' => 'required|string',
        ]);


    }
}
