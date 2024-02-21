<?php

namespace Sprintdigital\SprintBackup;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sprintdigital\SprintBackup\Commands\SprintBackupCommand;

class SprintBackupServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sprint-backup')
            ->hasCommand(SprintBackupCommand::class);
    }
}
