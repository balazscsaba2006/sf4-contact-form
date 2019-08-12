FROM php:7.2-fpm
ARG TIMEZONE
ARG HOST_IP

RUN apt-get update && apt-get install -y git unzip openssl procps acl zlib1g-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql bcmath zip

# Install xdebug
RUN pecl install xdebug-2.6.0

# Add custom config
COPY config/custom.ini $PHP_INI_DIR/conf.d/zzz_custom.ini

# Copy xdebug config
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20170718/xdebug.so" >> $PHP_INI_DIR/php.ini
COPY config/xdebug.ini $PHP_INI_DIR/conf.d/zzz_xdebug.ini
RUN printf '\nxdebug.remote_host="%s"\n', ${HOST_IP} >> $PHP_INI_DIR/conf.d/zzz_xdebug.ini
RUN cat $PHP_INI_DIR/conf.d/zzz_xdebug.ini

# Create an alias for Symfony console
RUN echo 'alias symfony="php bin/console"' >> ~/.bashrc

WORKDIR /var/www/symfony