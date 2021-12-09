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
        app('router')->aliasMiddleware('evocms-user-csrf', \EvolutionCMS\EvoUser\Middlewares\EvoUserCSRF::class);
        app('router')->aliasMiddleware('evocms-user-access', \EvolutionCMS\EvoUser\Middlewares\EvoUserAccess::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        //usage trans('evousercore::messages.line', [ 'field' => 'Username' ])
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'evocms-user-core');

        $this->publishes([
            __DIR__ . '/../publishable/assets'  => MODX_BASE_PATH . 'assets',
            __DIR__ . '/../publishable/configs' => EVO_CORE_PATH . 'custom/evocms-user/configs',
            __DIR__ . '/../publishable/lang' => EVO_CORE_PATH . 'custom/evocms-user/lang',
        ]);

        //usage trans('evousercustom::messages.line', [ 'field' => 'Username' ])
        $this->loadTranslationsFrom(EVO_CORE_PATH . 'custom/evocms-user/lang', 'evocms-user-custom');

        $this->mergeConfigFrom(
            EVO_CORE_PATH . 'custom/evocms-user/configs/evouser.php', 'evocms-user'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/Configs/default.php', 'evocms-user'
        );

    }
}