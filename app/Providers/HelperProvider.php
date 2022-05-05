<?php

namespace App\Providers;

use App\Helpers\AliOssHelper;
use App\Helpers\DownloadHelper;
use App\Helpers\ExcelHelper;
use App\Helpers\SmsHelper;
use Illuminate\Support\ServiceProvider;

class HelperProvider extends ServiceProvider
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
        $this->app->singleton('excel', function () {
            return new ExcelHelper();
        });
        $this->app->singleton('download', function () {
            return new DownloadHelper();
        });
        $this->app->singleton('sms', function () {
            return new SmsHelper();
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
