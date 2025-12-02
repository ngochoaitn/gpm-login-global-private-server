FROM php:8.1-apache

# Cài đặt các dependencies cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    nano \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql exif fileinfo

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy file source Laravel vào container
COPY . /var/www/html
WORKDIR /var/www/html

RUN mkdir -p storage/app/public/profiles

# Cấp quyền ghi cho storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Thiết lập quyền truy cập
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 775 /var/www/storage \
#     && chmod -R 775 /var/www/bootstrap/cache

# Kích hoạt mod_rewrite
RUN a2enmod rewrite

# Expose cổng 80
EXPOSE 80

RUN rm -rf public/storage
RUN php artisan storage:link
RUN php artisan key:generate

RUN chmod 777 /var/www/html/.env

CMD ["apache2-foreground"]
