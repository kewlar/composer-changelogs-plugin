Composer Changelogs Plugin
==========================

Whenever you do `composer update` or `composer install` this plugin displays links to GitHub's "Compare Changes" pages for 
each updated package, so you would have an easier time checking what's actually changed, e.g.
```
$ composer install
Loading composer repositories with package information
Updating dependencies (including require-dev)                                               
Prefetching 7 packages 🎶 💨
  - Downloading (100%)
    CHANGELOGS:
        https://github.com/doctrine/dbal/compare/v2.9.1...v2.9.2
        https://github.com/rollbar/rollbar-php/compare/v1.7.2...v1.7.4
Package operations: 0 installs, 10 updates, 0 removals
  - Updating doctrine/dbal (v2.9.1 => v2.9.2): Loading from cache
    CHANGELOG: https://github.com/doctrine/dbal/compare/v2.9.1...v2.9.2
  - Updating rollbar/rollbar (v1.7.2 => v1.7.4): Loading from cache
    CHANGELOG: https://github.com/rollbar/rollbar-php/compare/v1.7.2...v1.7.4
```

Installation
------------
```bash
composer require --dev kewlar/composer-changelogs-plugin ^0.1
```
That's it!
