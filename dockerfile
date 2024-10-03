FROM php:8.2

RUN apt-get update -y && \
    apt-get install -y \
        openssl \
        zip \
        unzip \
        git \
        libonig-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev # for pgsql driver, remove if not needed

RUN docker-php-ext-install pdo mbstring pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY laravel/ /app

RUN composer install

RUN php artisan migrate

CMD php artisan serve --host=0.0.0.0 --port=8000