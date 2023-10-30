<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
    /**
     * ext serve log.
     */
    public function render($request, Throwable $exception) 
    {
        if ($exception instanceof \Exception) {  
            if ($exception instanceof ValidationException) {
                $errorMessages = $exception->validator->errors()->all();
                $errorMessage = $errorMessages;
            } else {
                $errorMessage = [$exception->getMessage()];
            }

            \Log::error("Error: " . implode(', ', $errorMessage), ['exception' => $exception]);

            return response()->json([
                'message' => $errorMessage,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
