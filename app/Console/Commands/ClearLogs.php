<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    protected $signature = 'logs:clear';
    protected $description = 'Clear the Laravel log files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        file_put_contents(storage_path('logs/laravel.log'), ''); // Limpia el archivo de logs
        $this->info('Logs have been cleared!');
    }
}
