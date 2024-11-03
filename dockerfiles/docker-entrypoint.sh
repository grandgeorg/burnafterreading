#!/bin/bash

HASHED_PASSWORD=$(php -r "echo password_hash(getenv('ADMIN_PASSWORD'), PASSWORD_DEFAULT);")
export HASHED_ADMIN_PASSWORD=$HASHED_PASSWORD
echo "<?php return '$HASHED_ADMIN_PASSWORD';" > /var/www/BurnAfterReadingApp/pwd.php

# remove $ADMIN_PASSWORD from environment
unset ADMIN_PASSWORD

# remove $HASHED_PASSWORD from environment
unset HASHED_PASSWORD

# remove $HASHED_ADMIN_PASSWORD from environment
unset HASHED_ADMIN_PASSWORD

exec "$@"