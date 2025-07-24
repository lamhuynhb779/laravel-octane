# First Stage: Build Laravel Application (Composer Install)
FROM php:8.2-fpm AS builder

# Set working directory
WORKDIR /var/www

# Install dependencies (PHP extensions and system packages)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    curl \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql pcntl sockets \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the application code into the container
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Second Stage: Setup RoadRunner & PHP Extensions (for production environment)
FROM php:8.2-fpm AS runtime

# Set working directory
WORKDIR /var/www

# Install RoadRunner
RUN curl -sSL https://github.com/roadrunner-server/roadrunner/releases/download/v2.11.1/roadrunner-2.11.1-linux-amd64 -o /usr/local/bin/rr && \
    chmod +x /usr/local/bin/rr

# Copy the application files from the builder stage
COPY --from=builder /var/www /var/www

# Ensure correct permissions for Laravel files
RUN chown -R www-data:www-data /var/www

## Expose port 8080 for RoadRunner
#EXPOSE 8080
#
## Set the entrypoint for RoadRunner
#CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0"]
