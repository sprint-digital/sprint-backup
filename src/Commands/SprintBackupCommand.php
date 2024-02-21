<?php

namespace Sprintdigital\SprintBackup\Commands;

use Illuminate\Console\Command;

class SprintBackupCommand extends Command
{
    public $signature = 'backup:install';

    public $description = 'Publish the backup commands.';

    public function handle(): int
    {
        $this->info('Publishing backup commands...');

        if (! file_exists(app_path('Console/Commands'))) {
            mkdir(app_path('Console/Commands'), 0755, true);
        }

        copy(__DIR__.'/BackupRestore.php', app_path('Console/Commands/BackupRestore.php'));
        copy(__DIR__.'/BackupToSprint.php', app_path('Console/Commands/BackupToSprint.php'));
        $this->info('Backup commands files added to Commands folder');

        $this->info('Publishing backup config...');
        copy(__DIR__ . '/../../config/backup.php', config_path('backup.php'));
        $this->info('Backup config published');

        return self::SUCCESS;
    }
}
