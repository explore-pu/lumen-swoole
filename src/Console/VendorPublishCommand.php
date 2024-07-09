<?php

namespace LumenSwoole\Console;

use Illuminate\Console\Command;

class VendorPublishCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'swoole:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish config assets from swoole';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $source = __DIR__ . '/../../config/swoole.php';

        if (!is_dir(base_path('config'))) {
            mkdir(base_path('config'));
        }
        $dst = base_path('config/swoole.php');
        copy($source, $dst);

        $this->info('the assets publish success');
    }
}
