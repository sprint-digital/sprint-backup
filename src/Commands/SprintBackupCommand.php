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
        copy('BackupRestore.stub', 'app/Console/Commands/BackupRestore.php');
        copy('BackupToSprint.stub', 'app/Console/Commands/BackupToSprint.php');
        $this->info('Backup commands files added to Commands folder');

        return self::SUCCESS;
    }
}
