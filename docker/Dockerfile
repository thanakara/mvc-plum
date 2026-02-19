FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    # critical missing piece for PostgreSQL support: "libpg-dev"
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Composer:
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# OR:
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# If using framework:
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www