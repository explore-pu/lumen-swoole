<?php

namespace LumenSwoole\Console;

use Illuminate\Console\Command;

class SwooleWebsocketCommand extends Command
{
    protected $signature = 'swoole:websocket {action}';

    protected $description = 'Operate swoole websocket server with start|stop|restart|reload|status';

    /**
     * @var \Laravel\Lumen\Application|mixed
     */
    private mixed $log_file;

    /**
     * @var \Laravel\Lumen\Application|mixed
     */
    private mixed $pid_file;

    public function __construct()
    {
        parent::__construct();

        $this->log_file = config('swoole.websocket.log_file');
        $log_path = str_replace('/' . basename($this->log_file), '', $this->log_file);
        if (!is_dir($log_path)) {
            mkdir($log_path, 0755, true);
        }

        $this->pid_file = config('swoole.websocket.pid_file');
        $pid_path = str_replace('/' . basename($this->pid_file), '', $this->pid_file);
        if (!is_dir($pid_path)) {
            mkdir($pid_path, 0755, true);
        }
    }

    public function handle(): void
    {
        $action = $this->argument('action');
        switch ($action) {
            case 'start':
                $this->start();
                break;
            case 'stop':
                $this->stop();
                break;
            case 'restart':
                $this->restart();
                break;
            case 'reload':
                $this->reload();
                break;
            case 'status':
                $this->status();
                break;
            default:
                $this->info(
                    'Please type correct action.'.PHP_EOL.
                    '  start'.PHP_EOL.
                    '  stop'.PHP_EOL.
                    '  restart'.PHP_EOL.
                    '  reload'.PHP_EOL.
                    '  status'.PHP_EOL
                );
        }
    }

    /**
     * Start swoole
     *
     * @return void
     */
    protected function start(): void
    {
        if ($this->getPid()) {
            $this->error('swoole websocket server is already running');
            exit(1);
        }

        $this->info('starting swoole websocket server...');
        app()->make('swoole.websocket');
    }

    /**
     * Stop swoole
     *
     * @return void
     */
    protected function stop(): void
    {
        $this->info('immediately stopping...');
        $this->sendSignal(SIGTERM);
        $this->info('done');
    }

    /**
     * Restart swoole
     *
     * @return void
     */
    protected function restart(): void
    {
        $this->info('stopping swoole websocket server...');
        $pid = $this->sendSignal(SIGTERM);
        $time = 0;
        while (posix_getpgid($pid)) {
            usleep(100000);
            $time++;
            if ($time > 50) {
                $this->error('timeout...');
                exit(1);
            }
        }
        $this->info('done');
        $this->start();
    }

    /**
     * Reload swoole
     *
     * @return void
     */
    protected function reload(): void
    {
        $this->info('reloading...');
        $this->sendSignal(SIGUSR1);
        $this->info('done');
    }

    /**
     * Check the Swoole status
     *
     * @return void
     */
    protected function status(): void
    {
        $pid = $this->getPid();
        if ($pid) {
            $this->info('swoole websocket server is running. master pid : ' . $pid);
        } else {
            $this->error('swoole websocket server is not running!');
        }
    }

    /**
     * Send signal
     *
     * @param $sig
     * @return bool|int|void
     */
    protected function sendSignal($sig)
    {
        $pid = $this->getPid();
        if ($pid) {
            posix_kill($pid, $sig);
        } else {
            $this->error('swoole websocket is not running!');
            exit(1);
        }
        return $pid;
    }

    /**
     * Gets the process PID
     *
     * @return bool|int
     */
    protected function getPid(): bool|int
    {
        if (file_exists($this->pid_file)) {
            $pid = intval(file_get_contents($this->pid_file));
            if (posix_getpgid($pid)) {
                return $pid;
            } else {
                unlink($this->pid_file);
            }
        }

        return false;
    }
}
