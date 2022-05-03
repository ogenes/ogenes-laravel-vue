<?php

namespace App\Providers;

use App\Helpers\DownloadHelper;
use Illuminate\Support\ServiceProvider;

class DownloadProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('download', function () {
            return new DownloadHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
