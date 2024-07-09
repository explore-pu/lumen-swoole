<?php

namespace LumenSwoole;

use Illuminate\Support\ServiceProvider;

class SwooleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            Console\VendorPublishCommand::class,
            Console\SwooleHttpCommand::class,
            Console\SwooleWebsocketCommand::class,
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/swoole.php', 'swoole');

        $config = config('swoole');

        $this->app->singleton('swoole.http', function () use ($config) {
            $http_server = $config['http_server'];
            return new $http_server($config['http']);
        });

        $this->app->singleton('swoole.websocket', function () use ($config) {
            $websocket_server = $config['websocket_server'];
            return new $websocket_server($config['websocket']);
        });
    }
}
