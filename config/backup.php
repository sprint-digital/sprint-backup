<?php

return [
    'backup' => [

        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('BACKUP_AWS_PATH', 'laravel-backup'),

        'source' => [

            'files' => [

                /*
                 * The list of directories and files that will be included in the backup.
                 */
                'include' => [
                    base_path(),
                ],

                /*
                 * These directories and files will be excluded from the backup.
                 */
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                /*
                 * Determines if symlinks should be followed.
                 */
                'follow_links' => false,

               /*
                * This path is used to make directories in resulting zip-file relative
                * Set to false to include complete absolute path
                * Example: base_path()
                */
               'relative_path' => false,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL, PostgreSQL, SQLite and Mongo databases are supported.
             */
            'databases' => [
                'mysql',
            ],
        ],

        'destination' => [

            /*
             * The disk names on which the backups will be stored.
             */
            'disks' => [
                's3-backup',
            ],
        ],

        'notifications' => [

            'notifications' => [
                \Spatie\Backup\Notifications\Notifications\BackupHasFailed::class => [],
                \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound::class => [],
                \Spatie\Backup\Notifications\Notifications\CleanupHasFailed::class => [],
                \Spatie\Backup\Notifications\Notifications\BackupWasSuccessful::class => [],
                \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound::class => [],
                \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful::class => [],
            ],

            /*
            * Here you can specify the notifiable to which the notifications should be sent. The default
            * notifiable will use the variables specified in this config file.
            */
            'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

            'mail' => [
                'to' => 'your@example.com',

                'from' => [
                    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                    'name' => env('MAIL_FROM_NAME', 'Example'),
                ],
            ],

            'slack' => [
                'webhook_url' => '',

                /*
                * If this is set to null the default channel of the webhook will be used.
                */
                'channel' => null,

                'username' => null,

                'icon' => null,

            ],
        ],
        'variables' => [
            'BACKUP_ARCHIVE_PASSWORD' => env('BACKUP_ARCHIVE_PASSWORD', rand(100000, 999999)),
            'BACKUP_AWS_ACCESS_KEY_ID' => env('BACKUP_AWS_ACCESS_KEY_ID', null),
            'BACKUP_AWS_SECRET_ACCESS_KEY' => env('BACKUP_AWS_SECRET_ACCESS_KEY', null),
            'BACKUP_AWS_DEFAULT_REGION' => env('BACKUP_AWS_DEFAULT_REGION', 'ap-southeast-2'),
            'BACKUP_AWS_BUCKET' => env('BACKUP_AWS_BUCKET', null),
            'BACKUP_AWS_URL' => env('BACKUP_AWS_URL'),
            'BACKUP_AWS_PATH' => env('BACKUP_AWS_PATH'),
            'BACKUP_AWS_ENDPOINT' => env('BACKUP_AWS_ENDPOINT'),
            'BACKUP_AWS_USE_PATH_STYLE_ENDPOINT' => env('BACKUP_AWS_USE_PATH_STYLE_ENDPOINT', false),
            'BACKUP_MASTER_PASSWORD' => env('BACKUP_MASTER_PASSWORD', rand(100000, 999999)),
            'DB_DATABASE' => env('DB_DATABASE', 'forge'),
        ],
    ],
];
