<?php

namespace LumenSwoole\Swoole;

use Swoole\Http\Request;
use Swoole\Table;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebsocketServer
{
    private array $config = [];

    protected Server $server;

    protected Table $table;

    public function __construct($host, $port, $setting)
    {
        $this->server = new Server($host, $port);

        #set swoole http server configuration
        $this->server->set($setting);

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
        echo "[" .date('Y-m-d H:i:s') . "] swoole websocket server is started at " . $server->host . ":" . $server->port . "\n";
        echo "[" .date('Y-m-d H:i:s') . "] master_pid is " . $server->master_pid . "\n";
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
        echo "[" .date('Y-m-d H:i:s') . "] client【{$request->fd}】 connect\n";
    }

    /**
     * Set up the data table，key => value
     *
     * @return void
     */
    public function setTable()
    {
        $this->table->column('data', Table::TYPE_STRING, 255);
    }

    /**
     * To process messages
     *
     * @param Server $server
     * @param Frame $frame
     * @return void
     */
    public function onMessage(Server $server, Frame $frame)
    {
        echo "[" .date('Y-m-d H:i:s') . "] client【{$frame->fd}】 Incoming messages：{$frame->data}\n";

        // Parse the received message,$frame->data must contain the column name set by the setTable method
        $data = json_decode($frame->data, JSON_UNESCAPED_UNICODE);

        // you can use a model to process data
        // $users = User::query()->where('id', 1)->get();

        // client side data binding
        $this->table->set($frame->fd, $data);

        foreach ($this->table as $fd => $message) {
            echo "[" .date('Y-m-d H:i:s') . "] client【{$fd}】 of data：" . json_encode($message, JSON_UNESCAPED_UNICODE) . "\n";
        }

        // 发送消息给所有客户端
        foreach ($this->table as $fd => $message) {
            $server->push($fd, json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

    public function onClose(Server $server, $fd)
    {
        echo "[" .date('Y-m-d H:i:s') . "] client【{$fd}】 Shut down\n";

        $this->table->del($fd);
    }
}
