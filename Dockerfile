FROM php:8.2-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev

# Configurar GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensões PHP (AGORA COMPLETO)
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    zip \
    xml \
    fileinfo \
    intl

# Instalar curl (faltava!)
RUN apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_MEMORY_LIMIT=-1

WORKDIR /var/www

COPY . .

# Permissões Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 🔥 DEBUG TEMPORÁRIO (IMPORTANTE)
RUN composer install -vvv --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]