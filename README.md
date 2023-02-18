---
title: BurnAfterReading
---

# BurnAfterReading

Create ```pwd.php``` in ```BurnAfterReadingApp``` which returns a hashed password string.

Generate string with e.g.

```php
<?php
echo password_hash('MyGreatSecret', PASSWORD_DEFAULT);
```

Return it in ```pwd.php``` with:

```php
<?php
return '$2y$10$vH0sp9VPFAnAnw2OrSX8BOXfx2KSB2orwydGq1lsdMFk50h9oTDcW';
```
