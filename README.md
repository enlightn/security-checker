Enlightn Security Checker
===========================

The Enlightn Security Checker is a command line tool that checks if your
application uses dependencies with known security vulnerabilities. It uses the [Security Advisories Database](https://github.com/FriendsOfPHP/security-advisories).

Usage
-----

To check for security vulnerabilities in your dependencies, you may run the `security:check` command after a global composer require: 

```bash
php security-checker security:check /path/to/composer.lock
```

API
-----------

You may also use the API directly in your own code like so:

```php
use Enlightn\SecurityChecker\AdvisoryAnalyzer;
use Enlightn\SecurityChecker\AdvisoryFetcher;
use Enlightn\SecurityChecker\AdvisoryParser;
use Enlightn\SecurityChecker\Composer;

$parser = new AdvisoryParser((new AdvisoryFetcher)->fetchAdvisories());

$dependencies = (new Composer)->getDependencies('/path/to/composer.lock');

$result = (new AdvisoryAnalyzer($parser->getAdvisories()))->analyzeDependencies($dependencies);
```

The result above is in JSON format. The key is the package name and the value is an array of vulnerabilities based on your package version. An example is as below:

```json
{
  "laravel/framework":[
    {
      "title":"Unexpected bindings in QueryBuilder",
      "link":"https://blog.laravel.com/security-laravel-62011-7302-8221-released",
      "cve":null
    }
  ]
}
```
