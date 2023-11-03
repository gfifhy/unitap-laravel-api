<?php

namespace App\Http\Controllers\WebAuthn;

use App\Models\Role;
use App\Models\User;
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
        $res = $request->login();
        $user = User::where('id', $res->id)->first();
        $role = Role::find($user->role_id);
        $token = $user->createToken('token', ['*'], Carbon::now()->addDays(3))->plainTextToken;
        $cookie = cookie('auth_token', $token, 60*24*3, '/', null, true, true, false, 'None');
        return response()->noContent($res ? 204 : 422)->withCookie($cookie);
    }
}