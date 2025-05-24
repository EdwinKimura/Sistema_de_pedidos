# Usar imagem oficial PHP com FPM
FROM php:8.2-fpm

# Instalar dependências necessárias + nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql sockets

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar o código da aplicação para o container
COPY . .

# Copiar configuração customizada do Nginx (você deve criar esse arquivo)
COPY nginx.conf /etc/nginx/nginx.conf

# Instalar dependências PHP
RUN composer install --optimize-autoloader

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html

# Expor porta 80 para HTTP
EXPOSE 80

# Iniciar Nginx e PHP-FPM juntos
CMD service nginx start && php-fpm -F
