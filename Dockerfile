FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Força PHP-FPM a escutar na porta 9000
RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html
COPY . .

COPY nginx.conf /etc/nginx/nginx.conf

RUN composer install --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
