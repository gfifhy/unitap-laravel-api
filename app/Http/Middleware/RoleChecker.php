<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;
use App\Traits\ExceptionTrait;

class RoleChecker
{
    use ExceptionTrait;
    public function handle($request, Closure $next, ...$roles)
    {
        foreach($roles as $role) {
            if(auth()->user()->role->slug === $role){
                return $next($request);
            }
        }
        //echo auth()->user()->role->slug;
        $this->throwException('Unauthorized Access', '401' );
    }

}
