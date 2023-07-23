FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        git zip unzip libpq-dev libzip-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-install -j$(nproc) bcmath pdo_pgsql gd zip exif \
    && apt-get clean && apt-get autoclean

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer --version=2.3.3 \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc
