<?php

namespace Chopin\Console\Providers;

use Chopin\Support\LaravelServiceProvider;

/**
 * 
 * @deprecated
 *
 */
class AppServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
            ]);
        }
    }
}
