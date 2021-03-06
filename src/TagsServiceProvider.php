<?php

namespace Displore\Tags;

use Illuminate\Support\ServiceProvider;

class TagsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // Migrations.
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'displore.tags.migrations');
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        $this->app->singleton('tagger', function () {
            return new Tagger();
        });
    }
}
