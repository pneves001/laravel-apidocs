<?php

namespace Pneves001\Apidocs\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Routing\Route;
use Pneves001\Apidocs\Facades\Apidocs;
use Pneves001\Apidocs\Console\Commands\{
    GenerateApidocs,
    Install,
    MakeEndpoint,
    MakeWebhook,
    MakeParam
};
use Illuminate\Routing\PendingResourceRegistration;

class ApidocsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // prepare configuration
        //
        $this->mergeConfigFrom(
            __DIR__.'/../../config/apidocs.php', 'apidocs-config'
        );

        //
        // define default routes
        //
        \Illuminate\Support\Facades\Route::get(config('apidocs.uri'), function () {
            $apidocs = @file_get_contents(config('apidocs.file_path')) ?: '{"endpoints": []}';
            return view('apidocs::app')->with([
                'apidocs' => $apidocs
            ]);
        })->name('apidocs.docs');

        //
        // define stack routes
        //
        foreach(config('apidocs.stacks', []) as $name => $stack) {
            \Illuminate\Support\Facades\Route::get($stack['uri'], function () use ($stack) {
                $apidocs = @file_get_contents($stack['file_path']) ?: '{"endpoints": []}';
                return view('apidocs::app')->with([
                    'apidocs' => $apidocs
                ]);
            })->name("apidocs.docs.{$name}");
        }

        //
        // load views
        //
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'apidocs');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // publish configs
        //
        $this->publishes([
            __DIR__.'/../../config/apidocs.php' => config_path('apidocs.php'),
            __DIR__.'/../../publish/assets' => public_path('vendor/apidocs'),
        ]);

        //
        // define default endpoints grup
        //
        Apidocs::defineGroup('non-groupped', 'Non-groupped', 'Non-grouped endpoints');


        //
        // route macro
        //
        Route::macro('apidocs', function($data = NULL, string $stack = 'default'){
            return Apidocs::stack($stack)->registerRoute($data, $this);
        });

        //
        // resource routes macro
        //
        PendingResourceRegistration::macro('apidocs', function(array $data, string $stack = 'default'){
            apidocs($data, $stack);
        });

        //
        // register commands
        //
        if ($this->app->runningInConsole())
        {
            $this->commands([
                Install::class,
                GenerateApidocs::class,
                MakeEndpoint::class,
                MakeWebhook::class,
                MakeParam::class,
            ]);
        }
    }
}
