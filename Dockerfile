FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable mod_rewrite and allow .htaccess overrides
RUN a2enmod rewrite
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY . /var/www/html/
EXPOSE 80