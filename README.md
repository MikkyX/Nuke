# Take your website back to day zero... and rebuild

_**DISCLAIMER:** I wrote this package for [Larahack 2019](https://www.larahack.com) in an afternoon. It was a bit of fun. I don't actually suggest you use it anywhere. **I take no responsibility for anything that happens to your website if you do!**_

> I say we take off and nuke the entire site from orbit...
> [it's the only way to be sure.](https://www.youtube.com/watch?v=aCbfMkh940Q)

## Requirements

This package utilises the `migrate:fresh` command, therefore Laravel 5.5 is the minimum supported version.

## Installation

The package is installed via composer:

```bash
composer require mikkyx/nuke
```
Once you've done that, you should add the command class to `app/Console/Kernel.php`:

```php
protected $commands = [
    ...
    \MikkyX\Nuke\Commands\Nuke::class,
]
```
## Usage

The command is run as follows:

```bash
php artisan nuke
```

Once confirmed, the following actions will be taken:

* `migrate:fresh` will be executed to drop your database and rerun migrations
* `db:seed` will be executed to re-run any default database seeders
* `cache:clear` will be executed to clear out any cached views etc.
* `config:clear` will be executed to clear out the config cache
* All files (except `.gitignore`) in your configured `public` disk will be deleted
* `key:generate` will be executed to generate a new application key

**You will not be able to run this command if your environment is set to `live`, `master` or `production`.**

## Credits

* Written by [Michael Price](https://github.com/mikkyx) for [Larahack 2019](https://www.larahack.com) on the theme "We can rebuild"

## Licence

GPL v3. Please see the [License File](LICENSE) for more information.