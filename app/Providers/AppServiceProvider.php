<?php

namespace App\Providers;

use App\Services\Utils\FileService;
use App\Services\Utils\FileServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application Services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FileServiceInterface::class, FileService::class);
    }

    /**
     * Bootstrap any application Services.
     *
     * @return void
     */
    public function boot()
    {
        header_remove('X-Powered-By');
    }
}
