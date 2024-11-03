FROM php:8.2-apache

ARG ADMIN_PASSWORD
ARG ADMIN_SUBDIR
ARG TIMEZONE

ENV ADMIN_PASSWORD=$ADMIN_PASSWORD
ENV ADMIN_SUBDIR=$ADMIN_SUBDIR
ENV TZ=$TIMEZONE

# Install dependencies
RUN set -ex; \
    apt-get update; \
    docker-php-ext-install -j "$(nproc)" \
        opcache \
    ; \
    # Enable the required Apache modules
    a2enmod rewrite; \
    a2enmod remoteip;

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
ENV MAX_EXECUTION_TIME 600
ENV MEMORY_LIMIT 512M
ENV UPLOAD_LIMIT 512M
RUN set -ex; \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > $PHP_INI_DIR/conf.d/opcache-recommended.ini; \
    { \
        echo 'session.cookie_httponly=1'; \
        echo 'session.use_strict_mode=1'; \
    } > $PHP_INI_DIR/conf.d/session-strict.ini; \
    { \
        echo 'allow_url_fopen=Off'; \
        echo 'max_execution_time=${MAX_EXECUTION_TIME}'; \
        echo 'max_input_vars=10000'; \
        echo 'memory_limit=${MEMORY_LIMIT}'; \
        echo 'post_max_size=${UPLOAD_LIMIT}'; \
        echo 'upload_max_filesize=${UPLOAD_LIMIT}'; \
        echo 'date.timezone=${TZ}'; \
    } > $PHP_INI_DIR/conf.d/bar.ini;

WORKDIR /var/www/html

# make ADMIN_SUBDIR directory under /var/www/html
RUN mkdir -p /var/www/html/$ADMIN_SUBDIR

# copy index.html to /var/www/html
COPY dockerfiles/public/index.php /var/www/html

# copy admin/index.html to /var/www/html/$ADMIN_SUBDIR
COPY dockerfiles/public/admin/index.php /var/www/html/$ADMIN_SUBDIR

# copy ../public/bar/assets to /var/www/html
COPY public/bar/assets /var/www/html/assets

# make BurnAfterReadingApp directory under /var/www
RUN mkdir -p /var/www/BurnAfterReadingApp

# copy onfig.php and langs.php from BurnAfterReadingApp to /var/www/BurnAfterReadingApp
COPY BurnAfterReadingApp/config.php /var/www/BurnAfterReadingApp
COPY BurnAfterReadingApp/langs.php /var/www/BurnAfterReadingApp

# copy app, data, db, templates, vendor directories from BurnAfterReadingApp to /var/www/BurnAfterReadingApp
COPY BurnAfterReadingApp/app /var/www/BurnAfterReadingApp/app
COPY BurnAfterReadingApp/data /var/www/BurnAfterReadingApp/data
COPY BurnAfterReadingApp/db /var/www/BurnAfterReadingApp/db
COPY BurnAfterReadingApp/templates /var/www/BurnAfterReadingApp/templates
COPY BurnAfterReadingApp/vendor /var/www/BurnAfterReadingApp/vendor

# Make sure the data directory is writable by Apache.
RUN chown -R www-data:www-data /var/www/BurnAfterReadingApp/data

# Make sure BurnAfterReadingApp/db file is writable by Apache.
RUN chown -R www-data:www-data /var/www/BurnAfterReadingApp/db

# copy docker-entrypoint.sh to /
COPY dockerfiles/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT [ "/docker-entrypoint.sh" ]

# Start the Apache service
CMD ["apache2-foreground"]