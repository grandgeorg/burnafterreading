#!/bin/bash

# Setup admin subdirectory at runtime
if [ -n "$ADMIN_SUBDIR" ]; then
    mkdir -p "/var/www/html/$ADMIN_SUBDIR"
    cp /var/www/admin-index.php "/var/www/html/$ADMIN_SUBDIR/index.php"
fi

# Hash and store admin password
HASHED_PASSWORD=$(php -r "echo password_hash(getenv('ADMIN_PASSWORD'), PASSWORD_DEFAULT);")
echo "<?php return '$HASHED_PASSWORD';" > /var/www/BurnAfterReadingApp/pwd.php

# Remove sensitive variables from environment
unset ADMIN_PASSWORD
unset HASHED_PASSWORD

exec "$@"