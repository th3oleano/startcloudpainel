RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libpng-dev libxml2-dev libonig-dev \
    libjpeg-dev libfreetype6-dev libicu-dev libcurl4-openssl-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    zip \
    xml \
    intl \
    curl \
    fileinfo