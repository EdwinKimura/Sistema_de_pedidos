#!/bin/bash
set -e

if [ -n "$(ls -A migrations/*.php 2>/dev/null)" ]; then
    echo "Migrações existentes encontradas. Fazendo migrate..."
    php bin/console doctrine:migrations:migrate --no-interaction
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo "Nenhuma migração existente. Gerando e aplicando migração..."
    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate --no-interaction
    php bin/console doctrine:fixtures:load --no-interaction
fi

exec php-fpm
