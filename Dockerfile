# Final image
FROM php:8.3-apache

# Install dependencies
RUN apt-get update -y && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    ca-certificates \
    gnupg

RUN docker-php-ext-install pdo pdo_mysql zip

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions mbstring apcu opcache intl zip pdo pdo_mysql

# Create a non-root user
RUN useradd -m -d /home/symfony -s /bin/bash symfony

# Copy existing application directory contents
COPY . /var/www/symfony

RUN chown -R symfony:symfony /var/www/symfony

# Set working directory
WORKDIR /var/www/symfony

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Switch to non-root user
USER symfony

# Install Symfony dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]