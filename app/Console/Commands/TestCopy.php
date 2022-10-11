<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SSH;

class TestCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zerocool:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $remotePath = '/var/www/preview_laravel6_admin/public/*/**';
        $localPath = 'public/*/**';

        SSH::into('production')->run([
            'sudo -S',
            'df -h'
        ]);
        SSH::into('production')->get($remotePath, $localPath);
        $content =  SSH::into('production')->getString($remotePath, $localPath);

        SSH::into('production')
            ->run([
                $content
            ], function ($line) {
                // display output of command, by line
                echo $line;
            });

    }
}
