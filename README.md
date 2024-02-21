# This is my package sprint-backup

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sprint-digital/sprint-backup.svg?style=flat-square)](https://packagist.org/packages/sprint-digital/sprint-backup)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sprint-digital/sprint-backup/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sprint-digital/sprint-backup/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sprint-digital/sprint-backup/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sprint-digital/sprint-backup/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sprint-digital/sprint-backup.svg?style=flat-square)](https://packagist.org/packages/sprint-digital/sprint-backup)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/sprint-backup.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/sprint-backup)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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

You can publish the config file with:

```bash
php artisan backup:install
php artisan vendor:publish --tag="sprint-backup-config"
```

Backup database:

```bash
php artisan backup:to-sprint
```

Restore database:

```bash
php artisan backup:restore
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
