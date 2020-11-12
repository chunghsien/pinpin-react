<?php

namespace Chopin\Console\Providers;

use Chopin\Support\LaravelServiceProvider;
use Chopin\Console\Command\Resources\GulpApply;

class AppServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GulpApply::class,
            ]);
        }
    }
}
