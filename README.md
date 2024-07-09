## 安装
```
composer require explore-pu/lumen-swoole
```

## 配置

- 在bootstrap/app.php中注册service provider
```php
$app->register(LumenSwoole\SwooleServiceProvider::class);
```

- 发布配置文件
```shell
php artisan swoole:publish
```

- 同时在bootstrap/app.php加载此文件

```php
$app->configure('swoole');
```

## 使用

```shell
php artisan swoole:http start|restart|stop|reload|status

php artisan swoole:websocket start|restart|stop|reload|status
```
