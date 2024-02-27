<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BackupToSprint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:to-sprint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up data to Sprint.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Backup to Sprint started.');
        Artisan::call('config:cache');
        try {
            Artisan::call('backup:run --only-db');
        } catch (\Exception $e) {
            $this->error('Backup to Sprint failed.');
            return Command::FAILURE;
        }
        $this->info('Backup to Sprint ended.');
        return Command::SUCCESS;
    }
}
