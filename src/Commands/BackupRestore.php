<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Restore the database from a backup file and clean up production data.
 *
 * @description
 * Step 1: Show and select a backup file.
 * Step 2: Download and extract the backup file.
 * Step 3: Delete old database Tables.
 * Step 4: Restore the database.
 * Step 5: Clean up Database. ie. replace production data.
 */
class BackupRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {--last-backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database from a backup file and clean up the data.';

    /**
     * Tables to exclude from cleanup.
     *
     * @var array
     */
    private $excludedTables = ['migrations', 'password_resets', 'failed_jobs', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients', 'oauth_refresh_tokens', 'roles', 'permissions'];

    /**
     * Tables to truncate.
     *
     * @var array
     */
    private $tablesToTruncate = ['audits', 'activity_log', 'failed_jobs'];

    /**
     * Columns to clean.
     *
     * @var array
     */
    private $columnsToClean = ['email', 'name', 'first_name', 'last_name', 'full_name', 'xero_id', 'code', 'address', 'title', 'description', 'phone', 'number', 'tfn', 'abn'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('config:cache');

        // Step 1: Show and select a backup file.
        $isRestored = false;
        $backups = Storage::disk('s3-backup')->allFiles(config('backup.variables.BACKUP_AWS_PATH'));
        $this->info('The following backups are available:');
        $this->info('----------------------------------');
        foreach ($backups as $index => $backup) {
            $this->info(Str::padLeft($index + 1, 3, '0') . ': ' . $backup);
        }
        if ($this->option('last-backup')) {
            $backupNumber = count($backups);
        } else {
            $backupNumber = $this->ask('Which backup do you want to restore?');
        }
        $backupFile = $backups[$backupNumber - 1];
        $this->info('Selected ' . $backupFile);

        try {
            $date = substr(explode('/', str_replace('.zip', '', $backupFile), 2)[1], 0, 10);
            $timestamp = Carbon::parse($date)->setHour(0)->setMinute(0)->setSecond(0)->timestamp;
        } catch (\Exception $e) {
            $this->error('Fail to extract date.');

            return Command::FAILURE;
        }

        // Step 2: Download and extract the backup file.
        $contents = Storage::disk('s3-backup')->get($backupFile);
        Storage::disk('local')->put('restore/' . $backupFile, $contents);

        $zip = new ZipArchive();
        $res = $zip->open(storage_path('app/restore/' . $backupFile));
        if ($res === true) {
            $zip->setPassword(config('backup.variables.BACKUP_ARCHIVE_PASSWORD') . $timestamp);
            if (Storage::disk('local')->exists('restore')) {
                $this->info('Deleting old restore folder');
                Storage::disk('local')->deleteDirectory('restore');
            }
            $isExtracted = $zip->extractTo(storage_path('app/restore/content'));

            $zip->close();
            if ($isExtracted) {
                $this->info('Database backup extracted');
            } else {
                $this->error('Database backup extraction failed. Password may be incorrect.');

                return Command::FAILURE;
            }

            if (Storage::disk('local')->exists('restore/content/db-dumps')) {
                $files = Storage::disk('local')->allFiles('restore/content/db-dumps');
                $latestFile = $files[count($files) - 1];
                $this->info('The latest database backup is ' . $latestFile);
                $this->info('Dropping all tables in the database');

                // Step 3: Delete old database Tables.
                try {
                    DB::transaction(function () {
                        $tables = DB::select('SHOW TABLES');
                        foreach ($tables as $table) {
                            $tableArray = get_object_vars($table);
                            $tableName = $tableArray['Tables_in_' . config('backup.variables.DB_DATABASE')];
                            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                            $sql = "DROP TABLE`$tableName`;";
                            DB::statement($sql);
                            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        }
                    });
                } catch (\Exception $e) {
                }
                // Step 4: Restore the database.
                try {
                    $this->info('Restoring the database');
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    $command = 'cat storage/app/restore/content/db-dumps/mysql-homestead.sql | /usr/bin/mysql --host=' . config('database.connections.mysql.host') . ' -u ' . config('database.connections.mysql.username') . ' --password=' . config('database.connections.mysql.password') . ' ' . config('backup.variables.DB_DATABASE') . ' 2>&1';
                    exec($command);
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    $this->info('Database restored');
                    Storage::disk('local')->deleteDirectory('restore');
                    $isRestored = true;
                } catch (\Exception $e) {
                    $this->error('Database restore failed');
                    $this->error(substr($e->getMessage(), 0, 1000));

                    return Command::FAILURE;
                }
            } else {
                $this->error('Database backup not found');

                return Command::FAILURE;
            }
        } else {
            $this->error('Database backup extraction failed');

            return Command::FAILURE;
        }

        // Step 5: Clean up Database. ie. replace production data.
        try {
            DB::transaction(function () {
                $tables = DB::select('SHOW TABLES');
                foreach ($tables as $table) {
                    $tableArray = get_object_vars($table);
                    $tableName = $tableArray['Tables_in_' . config('backup.variables.DB_DATABASE')];
                    if (! in_array($tableName, $this->excludedTables)) {
                        $this->cleanTable($tableName);
                    }
                }
            });
        } catch (\Exception $e) {
            $this->error('Database cleanup failed');
            $this->error(substr($e->getMessage(), 0, 1000));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Clean up the data in the table.
     *
     * @param  string  $tableName
     */
    private function cleanTable($tableName)
    {
        if (Schema::hasColumn($tableName, 'email') && Schema::hasColumn($tableName, 'id')) {
            $sql = "UPDATE `$tableName` SET email = CONCAT(id,'@$tableName.com');";
            DB::statement($sql);
        }

        foreach ($this->columnsToClean as $column) {
            if ($tableName == 'users' && $column == 'email') {
                continue;
            }
            if (Schema::hasColumn($tableName, $column)) {
                $sql = "UPDATE `$tableName` SET $column = " . $this->randomSqlWord();
                DB::statement($sql);
            }
        }

        DB::commit();

        if (Schema::hasColumn($tableName, 'password') && $tableName == 'users') {
            DB::table($tableName)->update(['password' => bcrypt(config('backup.variables.BACKUP_MASTER_PASSWORD', rand(100000, 999999)))]);
        }

        if (in_array($tableName, $this->tablesToTruncate)) {
            DB::table($tableName)->truncate();
        }
    }

    /**
     * Generate a random SQL word.
     *
     * @param  int  $maxVariants
     * @return string
     */
    private function randomSqlWord($maxVariants = 100)
    {
        $faker = Factory::create();
        $sqlWords = "(CASE CEIL(RAND()*$maxVariants) ";
        for ($i = 1; $i <= $maxVariants; $i++) {
            $sqlWords .= "WHEN $i THEN '$faker->word' ";
        }
        $sqlWords .= 'END);';

        return $sqlWords;
    }
}
