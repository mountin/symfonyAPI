FROM php:8.2-fpm

# Install PostgreSQL PDO driver
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install  pdo pdo_pgsql pgsql

# Install necessary PHP extensions
RUN docker-php-ext-install bcmath

#sudo apt install php8.2-bcmath -y \
#sudo systemctl restart php8.2-fpm
