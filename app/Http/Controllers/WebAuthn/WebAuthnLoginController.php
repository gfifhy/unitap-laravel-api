<?php

namespace App\Http\Controllers\WebAuthn;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function options(AssertionRequest $request): Responsable
    {
        return $request->toVerify($request->validate(['email' => 'sometimes|email|string']));
    }

    /**
     * Log the user in.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertedRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(AssertedRequest $request): Response
    {
        $user = $request->login();
        $token = $user->createToken('token', ['*'], Carbon::now()->addDays(3))->plainTextToken;
        $cookie = cookie('auth_token', $token, 60*24*3, '/', null, env('SESSION_SECURE_COOKIE', true), true, false, 'None');
        //$user->role = Auth::user()->role;
        return response()->noContent()->withCookie($cookie);
    }
}
