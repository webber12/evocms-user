<?php

namespace EvolutionCMS\EvoUser;

use EvolutionCMS\ServiceProvider;

class EvoUserServiceProvider extends ServiceProvider
{
    protected $namespace = 'evouser';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadPluginsFrom(__DIR__ . '/../plugins/');

        $this->app->alias(EvoUser::class, 'evouser');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
