<?php

namespace ExplorePu\LumenSwoole;

use ExplorePu\LumenSwoole\Swoole\HttpServer;
use Illuminate\Support\ServiceProvider;

class SwooleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => base_path('config')], 'swoole-config');
        }
    }

    public function register(): void
    {
        $this->commands([
            Console\SwooleHttpCommand::class,
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/swoole.php', 'swoole');

        $this->app->singleton('swoole.http', function ($app) {
            $config = $app['config']['swoole'];

            return new HttpServer($config);
        });
    }
}
