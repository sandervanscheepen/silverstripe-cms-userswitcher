# Silverstripe CMS User Switcher

Allows administrators to assume the identity of another user.

Silverstripe 4+

## Installation
```
composer require "sandervanscheepen/silverstripe-cms-userswitcher"
```

## Usage
The ability to assume the identity of another user can exclusively be enabled for Members with admin permissions.
All settings are managed through the Security admin at /admin/security
Admin users can enable/disable the userswitcher dropdown on their own account and add other accounts to the dropdown that will apear at the top left of the CMS interface (refresh needed after activation).

Options for Member that is part of Administrators group (viewed by administrator - also note the added dropdown in the top left corner):
![Settings for a Member record that is part of Administrators group](/docs/images/screen_security_admin.jpg?raw=true "Settings for a Member record that is part of Administrators group")

Options for Member that is not part of Administrators group (viewed by administrator):
![Settings for a Member record that is not part of Administrators group](/docs/images/screen_security_nonadmin.jpg?raw=true "Settings for a Member record that is not part of Administrators group")


## License
See [License](license.md)

## Maintainers
 * Sander van Scheepen <sander@hamaka.nl>
