---
title: BurnAfterReading
---

# BurnAfterReading

## Requirements

Requires PHP 7.1 or higher. PHP 8.x is recommended.
Requires php-sqlite3 extension.

## Installation

1. Install php-sqlite3 extension under Ubuntu.

```bash
sudo apt-get install php-sqlite3
# Or a concrete version e.g.
sudo apt-get install php8.1-sqlite3
```

2. Create ```pwd.php``` file in ```BurnAfterReadingApp``` which returns a hashed password string.

Generate string in console with e.g.

```bash
php -a
```

```php
echo password_hash('MyGreatSecret', PASSWORD_DEFAULT);
```

Copy the String into ```pwd.php``` as a return statement with:

```php
<?php
return '$2y$10$vH0sp9VPFAnAnw2OrSX8BOXfx2KSB2orwydGq1lsdMFk50h9oTDcW';
```
