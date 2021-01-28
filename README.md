Enlightn Security Checker
===========================

![tests](https://github.com/enlightn/security-checker/workflows/tests/badge.svg?branch=main)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/enlightn/security-checker/v/stable?format=flat-square)](https://packagist.org/packages/enlightn/security-checker)
[![Total Downloads](https://img.shields.io/packagist/dt/enlightn/security-checker.svg?style=flat-square)](https://packagist.org/packages/enlightn/security-checker)

The Enlightn Security Checker is a command line tool that checks if your
application uses dependencies with known security vulnerabilities. It uses the [Security Advisories Database](https://github.com/FriendsOfPHP/security-advisories).

Usage
-----

To check for security vulnerabilities in your dependencies, you may run the `security:check` command after a global composer require: 

```bash
php security-checker security:check /path/to/composer.lock
```

This command will return a success status code of `0` if there are no vulnerabilities and `1` if there is at least one vulnerability.

API
-----------

You may also use the API directly in your own code like so:

```php
use Enlightn\SecurityChecker\SecurityChecker;

$result = (new SecurityChecker)->check('/path/to/composer.lock');
```

The result above is in JSON format. The key is the package name and the value is an array of vulnerabilities based on your package version. An example is as below:

```json
{
  "laravel/framework": {
    "version": "8.22.0",
    "time": "2021-01-13T13:37:56+00:00",
    "advisories": [{
      "title": "Unexpected bindings in QueryBuilder",
      "link": "https://blog.laravel.com/security-laravel-62011-7302-8221-released",
      "cve": null
    }]
  }
}
```

## Contribution Guide

Thank you for considering contributing to the Enlightn security-checker project! The contribution guide can be found [here](https://www.laravel-enlightn.com/docs/getting-started/contribution-guide.html).

## License

The Enlightn security checkers licensed under the [MIT license](LICENSE.md).
