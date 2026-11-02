FROM php:8.2.35-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libonig-dev \
    && docker-php-ext-install -j"$(nproc)" pgsql pdo_pgsql mbstring mysqli opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/ncc

COPY . .
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-ncc.ini
COPY docker/apache-ncc.conf /etc/apache2/conf-available/ncc.conf

RUN a2enconf ncc \
    && mkdir -p /var/www/html/ncc/log \
    && chown www-data:www-data /var/www/html/ncc/log

EXPOSE 80

CMD ["apache2-foreground"]
