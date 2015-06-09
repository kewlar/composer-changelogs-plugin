Composer Changelogs Plugin
==========================

This plugin allows easier access to package changelogs, when you want to check what's actually been changed, before
doing `composer update` in your project.

**At the moment this is only a quick spike, without any tests, regard for safety, or whatever. DO NOT use it in
production.**

Usage
-----

Add this plugin to your composer.json require-dev section, like this:

    "require-dev": {
        "kewlar/composer-changelogs-plugin": "*@dev"
    }

And whenever you do `composer update`, the plugin will print links to GitHub's "Compare Changes" pages for each updated
package, so you would have an easier time checking what's actually changed.
