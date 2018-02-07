FROM php:7.1

WORKDIR /var/www


RUN apt-get update \
    && apt-get install -y libpq-dev git unzip \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "alias migrate-test='(cd /var/www/html/; export environment=test; ./db.sh db:migrate)'" >> /etc/bash.bashrc \
    && echo "alias migrate-prod='(cd /var/www/html/; export environment=prod; ./db.sh db:migrate)'" >> /etc/bash.bashrc \
    && apt-get clean && apt-get -y autoremove \
    && rm -rf /var/lib/apt/lists/*

RUN rm -rf /var/www/html

COPY . /var/www/

RUN ./composer.phar install --no-dev