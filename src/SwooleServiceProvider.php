<?php

namespace XiaoZhi\LumenSwoole;

use Illuminate\Support\ServiceProvider;
use XiaoZhi\LumenSwoole\Swoole\HttpServer;
use XiaoZhi\LumenSwoole\Swoole\WebsocketServer;

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
            return new HttpServer($config['http']);
        });

        $this->app->singleton('swoole.websocket', function () use ($config) {
            $websocket_server = $config['websocket']['server'];
            return new $websocket_server($config['websocket']);
        });
    }
}
