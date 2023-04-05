<?php

namespace App\Traits;
use Illuminate\Validation\ValidationException;
trait ExceptionTrait {
    private function throwException($message, $errorCode) {
        throw ValidationException::withMessages([
            'message' => $message,
            'code' => $errorCode
        ]);
    }
}
