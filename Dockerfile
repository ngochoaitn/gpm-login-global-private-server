# FROM php:8.2-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     nano

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Create directory for PHP configuration
# RUN mkdir -p /usr/local/etc/php/conf.d

# # Install PHP extensions
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# # Get latest Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www/html

# # Copy existing application directory
# COPY . .

# RUN rm -rf public/storage
# RUN php artisan storage:link
# RUN php artisan key:generate
# RUN chmod -Rf 777 ./storage
# RUN chmod 777 /var/www/html/.env

# # Install dependencies
# RUN composer install

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache


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
# RUN chmod -Rf 777 ./storage
RUN chmod 777 /var/www/html/.env

CMD ["apache2-foreground"]
