# Usar uma imagem oficial do PHP como base
FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

RUN docker-php-ext-install sockets

# Baixar e instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho no contêiner
WORKDIR /var/www/html

# Copiar o restante do código do projeto para o contêiner
COPY . .

# Copiar o arquivo de configuração customizado do Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Instalar dependências do PHP
RUN composer install --optimize-autoloader

# Ajustar permissões (se necessário)
RUN chown -R www-data:www-data /var/www/html

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# Expor a porta do PHP-FPM
EXPOSE 9000