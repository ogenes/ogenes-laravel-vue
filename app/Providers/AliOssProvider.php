<?php

namespace App\Providers;

use App\Helpers\AliOssHelper;
use Illuminate\Support\ServiceProvider;

class AliOssProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('aliOss', function () {
            return new AliOssHelper();
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
