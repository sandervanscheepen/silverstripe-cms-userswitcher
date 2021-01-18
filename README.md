# Silverstripe CMS User Switcher

Allow Admin users to switch to other account through the CMS menu.

Silverstripe 4+

## Installation
First: add custom repository to composer.json
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/sandervanscheepen/silverstripe-cms-userswitcher.git"
        }
    ],
```
Second: install via composer:

```
composer require "sandervanscheepen/silverstripe-cms-userswitcher"
```

## Usage
Enabled for Members through the Security section in the CMS.
Only Members in administrator group can be allowed to see the dropdown in the top left corner of the CMS to switch to other accounts.
All Members (admin or non admin) can be added to that dropdown through their record in Security.

## License
See [License](license.md)

## Maintainers
 * Sander van Scheepen <sander@hamaka.nl>
