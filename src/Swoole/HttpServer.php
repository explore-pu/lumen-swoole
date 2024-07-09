<?php

namespace LumenSwoole\Swoole;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\http\Server;
use Illuminate\Http\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class HttpServer
{
    private Server $server;

    private $app;

    public function __construct($config)
    {
        $this->server = new Server($config['host'], $config['port']);

        unset($config['host'], $config['port']);

        #set swoole http server configuration
        $this->server->set($config);

        $this->server->on('start', array($this, 'onStart'));
        $this->server->on('workerStart', array($this, 'onWorkerStart'));
        $this->server->on('request', array($this, 'onRequest'));

        $this->server->start();
    }

    public function onStart($server): void
    {
        echo "[" .date('Y-m-d H:i:s') . "] swoole http server is started at http://" . $server->host . ":" . $server->port . "\n";
        echo "[" .date('Y-m-d H:i:s') . "] master_pid is " . $server->master_pid . "\n";
    }

    public function onWorkerStart($server, $worker_id): void
    {
        // 应用初始化
        $this->app = require base_path('bootstrap/app.php');
    }

    public function onRequest(Request $request, Response $response): void
    {
        // 处理用户请求
        $http_request = $this->parseRequest($request);
        // 处理用户响应
        $http_response = $this->app->dispatch($http_request);
        // 响应用户请求
        $content = $this->parseResponse($response, $http_response);

        $response->end($content);
    }

    protected function parseRequest(Request $request): HttpRequest
    {
        $get = $request->get ?? [];
        $post = $request->post ?? [];
        $cookie = $request->cookie ?? [];
        $server = $request->server ?? [];
        $header = $request->header ?? [];
        $files = $request->files ?? [];
        $fast_cgi = [];

        $new_server = array();
        foreach ($server as $key => $value) {
            $new_server[strtoupper($key)] = $value;
        }

        foreach ($header as $key => $value) {
            $new_server['HTTP_' . strtoupper($key)] = $value;
        }

        $content = $request->rawContent() ?: null;

        return new HttpRequest($get, $post, $fast_cgi, $cookie, $files, $new_server, $content);
    }

    protected function parseResponse(Response $response, HttpResponse $http_response): bool|string
    {
        $response->status($http_response->getStatusCode());

        foreach ($http_response->headers->allPreserveCase() as $name => $values) {
            foreach ($values as $value) {
                $response->header($name, $value);
            }
        }

        foreach ($http_response->headers->getCookies() as $cookie) {
            $response->rawcookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }

        return $http_response->getContent();
    }
}
