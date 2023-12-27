<?php

namespace App\Providers;

use App\Traits\ExceptionTrait;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    use ExceptionTrait;
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api/auth/auth.php'));

            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/student')
                ->group(base_path('routes/api/student/student.php'));


            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/admin')
                ->group(base_path('routes/api/admin/admin.php'));

            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/security-guard')
                ->group(base_path('routes/api/guard/guard.php'));

            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/store')
                ->group(base_path('routes/api/store/store.php'));

            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api/guidance')
                ->group(base_path('routes/api/guidance/guidance.php'));

            Route::prefix('api/landing')
                ->group(base_path('routes/api/landing.php'));

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60 )->by($request->user()?->id ?: $request->ip())->response(function (Request $request){
                return $this->throwException('Too many request. Try again later', '429');
            });
        });

        RateLimiter::for('loginThrottle', function (Request $request) {
        return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())->response(function (Request $request){
            return $this->throwException('Too many request. Try again later', '429');
        });
    });
    }
}
