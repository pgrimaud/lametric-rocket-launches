FROM php:7.3-apache

ENV APP_PATH=/var/www/html

RUN apt-get update && apt-get install -y -qq \
    curl \
    zip \
    unzip \
    libpq-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN sed -i -e "s/html/html\/public/g" /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

COPY . ${APP_PATH}

RUN composer install

RUN chown -R www-data:www-data ${APP_PATH}

ENTRYPOINT []
CMD docker-php-entrypoint apache2-foreground
