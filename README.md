# Sprint backup - LARAVEL v8 ONLY

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sprint-digital/sprint-backup.svg?style=flat-square)](https://packagist.org/packages/sprint-digital/sprint-backup)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sprint-digital/sprint-backup/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sprint-digital/sprint-backup/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sprint-digital/sprint-backup/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sprint-digital/sprint-backup/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sprint-digital/sprint-backup.svg?style=flat-square)](https://packagist.org/packages/sprint-digital/sprint-backup)

Back up and secure database to AWS S3. Then redistribute the backup to other team environments.

DO NOT SKIP THIS STEP!!! please read AWS setup [HERE](https://sprintdigital.atlassian.net/l/cp/kr9F1uHP).


## Installation

You can install the package via composer:

```bash
composer require sprint-digital/sprint-backup
```

Add environment variables to your `.env` file:

```bash
BACKUP_AWS_ACCESS_KEY_ID=
BACKUP_AWS_SECRET_ACCESS_KEY=
BACKUP_AWS_DEFAULT_REGION=ap-southeast-2
BACKUP_AWS_BUCKET=sprint-db-bucket
BACKUP_AWS_USE_PATH_STYLE_ENDPOINT=false
BACKUP_ARCHIVE_PASSWORD="{secret}"
BACKUP_AWS_PATH="{project repository name}"
BACKUP_MASTER_PASSWORD="{secret}"
```

Add the following to your `config/filesystems.php` in `disks` array:

```php
's3-backup' => [
    'driver' => 's3',
    'key' => env('BACKUP_AWS_ACCESS_KEY_ID'),
    'secret' => env('BACKUP_AWS_SECRET_ACCESS_KEY'),
    'region' => env('BACKUP_AWS_DEFAULT_REGION'),
    'bucket' => env('BACKUP_AWS_BUCKET'),
    'url' => env('BACKUP_AWS_URL') . '/' . env('BACKUP_AWS_PATH'),
    'endpoint' => env('BACKUP_AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('BACKUP_AWS_USE_PATH_STYLE_ENDPOINT', false),
    'throw' => false,
],
```

You can publish the config file with:

```bash
php artisan backup:install
```

Backup database:

```bash
php artisan backup:to-sprint
```

Restore database:

```bash
php artisan backup:restore
```

In some cases the .sql file is too large to be restored. In those cases, you should just manually restore the database and then run the following command to clean up the database:

```bash
// Use this in local docker/local environment
cat storage/app/restore/content/db-dumps/mysql-homestead.sql | docker exec -i {CONTAINER_NAME} /usr/bin/mysql -u homestead --password=password homestead 
```


## Production / Staging

You might have to install mysql on your server. (ie. worker servers)

```bash
sudo apt-get install -y default-mysql-client
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hoang Ho](https://github.com/na)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
