<?php

namespace XiaoZhi\LumenSwoole\Swoole;

use Swoole\Http\Request;
use Swoole\Table;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebsocketServer
{
    private array $config = [];

    protected Server $server;

    protected Table $table;

    public function __construct($config)
    {
        $this->config = $config;

        $this->server = new Server($this->config['host'], $this->config['port']);

        unset($this->config['host'], $this->config['port'], $this->config['server']);

        #set swoole http server configuration
        $this->server->set($this->config);

        $this->server->on('start', array($this, 'onStart'));
        $this->server->on('open', array($this, 'onOpen'));
        $this->server->on('workerStart', array($this, 'onWorkerStart'));
        $this->server->on('message', array($this, 'onMessage'));
        $this->server->on('close', array($this, 'onClose'));

        // 设置数据表
        $this->table = new Table(1024);
        $this->setTable();
        $this->table->create();

        $this->server->start();
    }

    public function onStart($server): void
    {
        echo "swoole websocket server is started at http://" . $server->host . ":" . $server->port . "\n";
        echo "master_pid is " . $server->master_pid . "\n";
    }

    public function onWorkerStart($server, $worker_id): void
    {
        // 应用初始化
        $app = require base_path('bootstrap/app.php');

        $capsule = $app->make('Illuminate\Database\Capsule\Manager');
        // 创建链接
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'lumen',
            'username'  => 'jammy',
            'password'  => 'secret',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        // 设置全局静态可访问
        $capsule->setAsGlobal();
        // 启动Eloquent
        $capsule->bootEloquent();
    }

    public function onOpen(Server $server, Request $request)
    {
        echo "client【{$request->fd}】 connect\n";

        $this->table->set($request->fd, []);
    }

    public function onClose(Server $server, $fd)
    {
        echo "client【{$fd}】 Shut down\n";

        $this->table->del($fd);
    }
}
