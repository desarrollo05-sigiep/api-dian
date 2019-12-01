Permisos de directorio:

    chmod -Rv 777 storage bootstrap
Actualizar composer:
    
    composer update
Configure el archivo .env (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME y DB_PASSWORD).
Ejecute las migraciones y los sembradoras:

    php artisan migrate --seed

Servidor de desarrollo local:

    php artisan serve