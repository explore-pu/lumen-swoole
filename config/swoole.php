<?php

return [
    'host' => '0.0.0.0',
    'port' => 9501,
    'worker_num' => 4, // 设置启动的Worker进程数量
    'daemonize' => true, // 是否转入后台运行
    'max_conn' => 10000, // 此参数用来设置Server最大允许维持多少个tcp连接，超过此数量后，新进入的连接将被拒绝
    'max_request' => 1000, // 此参数表示worker进程在处理完n次请求后结束运行，manager会重新创建一个worker进程。此选项用来防止worker进程内存溢出
    'dispatch_mode' => 2, //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
    'debug_mode' => true,
    'log_file' => storage_path('logs/swoole/http.log'),// 日志文件路径
    'pid_file' => storage_path('app/swoole/http.pid'),// 进程文件路径
    'heartbeat_check_interval' => 5,// 每隔多少秒检测一次，单位秒，Swoole会轮询所有TCP连接，将超过心跳时间的连接关闭
    'heartbeat_idle_time' => 10, // TCP连接的最大闲置时间，单位秒, 如果某fd最后一次发包距离现在的时间超过这个时间，连接将关闭
];
