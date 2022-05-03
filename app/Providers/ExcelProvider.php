<?php

namespace App\Providers;

use App\Helpers\ExcelHelper;
use Illuminate\Support\ServiceProvider;

class ExcelProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('excel', function () {
            return new ExcelHelper();
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
