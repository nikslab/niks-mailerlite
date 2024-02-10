# Use the official PHP 8.0 image as the base image
FROM php:8.0-fpm

# Install required extensions
RUN docker-php-ext-install pdo_mysql

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the contents of the current directory to the working directory
COPY . /var/www/html

# Expose port 9000 to communicate with PHP-FPM
EXPOSE 9000

# Set up Nginx configuration
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Start PHP-FPM
CMD ["php-fpm"]
