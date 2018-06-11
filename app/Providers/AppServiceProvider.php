<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Clean up new lines in strings from legacy sources
         *
         * @param string $string
         * @return string
         */
        Str::macro('normalizedNewLines', function ($string) {
            return str_replace(["\r\n", "\r"], "\n", $string);
            ;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
