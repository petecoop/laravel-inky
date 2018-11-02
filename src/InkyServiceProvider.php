<?php

namespace fibis\LaravelInky;

use Illuminate\Support\ServiceProvider;

class InkyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerExtension();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $resolver = $app['view.engine.resolver'];
        
        $app->singleton('inky.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];
            
            return new InkyCompiler($app['blade.compiler'], $app['files'], $cache);
        });

        $resolver->register('inky', function () use ($app) {
            return new InkyCompilerEngine($app['inky.compiler'], $app['files']);
        });
    }
    
    protected function registerExtension()
    {
        $this->app['view']->addExtension('inky.php', 'inky');
    }

}
