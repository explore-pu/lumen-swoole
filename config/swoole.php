<?php

return [
    'http_server' => LumenSwoole\Swoole\HttpServer::class,// 可替换成自己的server
    'http' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'worker_num' => 4, // 设置启动的Worker进程数量,默认值：CPU 核数
        'daemonize' => true, // 是否转入后台运行
        'max_conn' => 1024, // 此参数用来设置 Server 最大允许维持多少个 TCP 连接。超过此数量后，新进入的连接将被拒绝
        'max_request' => 1000, // 设置 worker 进程的最大任务数,一个 worker 进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源,默认值：0 即不会退出进程
        'dispatch_mode' => 2, //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
        'debug_mode' => true,
        'log_file' => storage_path('logs/swoole/http.log'),// 日志文件路径
        'pid_file' => storage_path('app/swoole/http.pid'),// 进程文件路径
        'heartbeat_check_interval' => 5,// 每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭
        'heartbeat_idle_time' => 10, // TCP连接的最大闲置时间，单位秒, 如果某fd最后一次发包距离现在的时间超过这个时间，连接将关闭
    ],
    'websocket_server' => LumenSwoole\Swoole\WebsocketServer::class,// 可替换成自己的server
    'websocket' => [
        'host' => '0.0.0.0',
        'port' => 9502,
        'worker_num' => 4, // 设置启动的Worker进程数量,默认值：CPU 核数
        'daemonize' => true, // 是否转入后台运行
        'max_conn' => 1024, // 此参数用来设置 Server 最大允许维持多少个 TCP 连接。超过此数量后，新进入的连接将被拒绝
        'max_request' => 1000, // 设置 worker 进程的最大任务数,一个 worker 进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源,默认值：0 即不会退出进程
        'dispatch_mode' => 2, //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
        'debug_mode' => true,
        'log_file' => storage_path('logs/swoole/websocket.log'),// 日志文件路径
        'pid_file' => storage_path('app/swoole/websocket.pid'),// 进程文件路径
//        'heartbeat_check_interval' => 5,// 每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭
//        'heartbeat_idle_time' => 10, // TCP连接的最大闲置时间，单位秒, 如果某fd最后一次发包距离现在的时间超过这个时间，连接将关闭
    ]
];
