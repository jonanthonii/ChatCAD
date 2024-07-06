
# Use the official PHP image with Apache
FROM php:8.2-apache

# Install PHP extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set the working directory
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Enable Apache modules
RUN a2enmod rewrite

# Restart Apache
RUN service apache2 restart

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]