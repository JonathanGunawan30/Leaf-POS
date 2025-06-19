FROM dunglas/frankenphp:latest

WORKDIR /app

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    gd \
    pdo_mysql \
    zip \
    pcntl \
    bcmath \
    mbstring \
    exif

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app

RUN mkdir -p storage/logs \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/app/public \
             bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

RUN composer require laravel/octane

RUN php artisan octane:install --server=frankenphp

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

RUN chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "octane:start", \
     "--server=frankenphp", \
     "--host=0.0.0.0", \
     "--port=8000", \
     "--workers=4", \
     "--task-workers=2", \
     "--max-requests=1000"]
