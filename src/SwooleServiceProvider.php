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

        $this->app->singleton('swoole.http', function () {
            $http_server = config('swoole.http.server');
            return new $http_server(config('swoole.http.host'), config('swoole.http.port'), config('swoole.http.setting'));
        });

        $this->app->singleton('swoole.websocket', function () {
            $websocket_server = config('swoole.websocket.server');
            return new $websocket_server(config('swoole.http.host'), config('swoole.http.port'), config('swoole.http.setting'));
        });
    }
}
