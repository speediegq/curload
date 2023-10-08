![img](resources/curload-white.svg)

# curload

curload is a simple file uploading site allowing users to upload files.

## Features

- File uploading (with or without an account)
- Account system, including administrators,  temporary accounts and registering
- Administration tools, useful for creating accounts, moderating files and more.
- Modern user interface, customizable through CSS, PHP and JavaScript.
- User pages, useful for terms of service, privacy policy, rules, etc.
- GPL licensed (no proprietary software here)

## Dependencies

- php
- sqlite3
- Web server (optional, sort of)

- On Gentoo, you'll need to enable USE flag `sqlite` for package `dev-lang/php`
in case you're testing locally using `php -S`.

- On Debian, you'll need to install the appropriate Apache
plugin if you want to use Apache.

## Installation

1. Set up a web server with php and sqlite3
2. Point it to `index.php`

When no admin key is set up, you'll be prompted to create a primary admin key.
The primary admin key is able to do absolutely everything, while normal admin
keys are able to do everything except view and modify other administrators.

## Hacking

To hack on curload, you can modify `config.ini`. This is a configuration file
that contains all the default options. You can also add pages. You can do this
by creating a PHP script (see `about.php` for an example) as well as a
`.name` file.

You can modify all of the PHP, but I've tried to keep it sort of user friendly.
It is possible to style curload by using CSS classes and IDs. It is also
possible to include JavaScript.

## Examples

See examples/ for some useful PHP example scripts.

## License

GNU Affero General Public License version 3.0. See `COPYING` for details.
