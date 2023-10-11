<?php

namespace XiaoZhi\LumenSwoole\Library;

use App\Models\User;
use Swoole\Table;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use XiaoZhi\LumenSwoole\Swoole\WebsocketServer;

class HandleMessage extends WebsocketServer
{
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
        echo "client【{$frame->fd}】 Incoming messages：{$frame->data}\n";

        // Parse the received message,$frame->data must contain the column name set by the setTable method
        $data = json_decode($frame->data, JSON_UNESCAPED_UNICODE);

        // you can use a model to process data
        // $users = User::query()->where('id', 1)->get();

        // client side data binding
        $this->table->set($frame->fd, $data);

        foreach ($this->table as $fd => $message) {
            echo "client【{$fd}】 of data：" . json_encode($message, JSON_UNESCAPED_UNICODE) . "\n";
        }

        // 发送消息给所有客户端
        foreach ($this->table as $fd => $message) {
            $server->push($fd, json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }
}
