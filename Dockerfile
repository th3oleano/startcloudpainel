# Usar a imagem base com PHP e Composer já instalados
FROM php:8.3-cli-alpine

# Definir o diretório de trabalho
WORKDIR /var/www

# Copiar composer.json e composer.lock para o diretório de trabalho
COPY composer.json composer.lock ./

# Copiar o restante da aplicação (incluindo o arquivo artisan)
COPY . .

# Instalar as dependências do sistema e extensões PHP necessárias para Laravel
RUN apk add --no-cache \
        bash \
        curl \
        git \
        zip \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        postgresql-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo pdo_pgsql zip


# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependências do Composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Rodar o servidor embutido do Laravel (serve) e aceitar conexões de qualquer IP, usando a porta 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
