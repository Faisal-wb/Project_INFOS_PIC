FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader || true

# Copy wait-for-it or just rely on restart policy
# Expose port 8080
EXPOSE 8080

# Start development server
CMD php artisan storage:link || true && php artisan serve --host=0.0.0.0 --port=8080
