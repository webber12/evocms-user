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
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
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
        $this->publishes([
            __DIR__ . '/../publishable/assets'  => MODX_BASE_PATH . 'assets',
            __DIR__ . '/../publishable/configs' => EVO_CORE_PATH . 'custom/evocms-user/configs',
        ]);
    }
}
