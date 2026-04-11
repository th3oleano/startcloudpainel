FROM php:8.2-fpm

# Instalar dependências do sistema
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
    libfreetype6-dev

# Configurar GD corretamente
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Instalar extensões PHP necessárias
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    zip \
    xml \
    fileinfo

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Evitar erro de memória
ENV COMPOSER_MEMORY_LIMIT=-1

# Diretório da aplicação
WORKDIR /var/www

# Copiar arquivos
COPY . .

# Permissões (importante pro Laravel)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Instalar dependências
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Expor porta
EXPOSE 9000

CMD ["php-fpm"]