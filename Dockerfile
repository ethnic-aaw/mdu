FROM php:8.2-apache
RUN apt-get update && apt-get install -y libsqlite3-dev unzip \
 && docker-php-ext-install pdo pdo_sqlite \
 && a2enmod rewrite headers \
 && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY . /var/www/html/
RUN mkdir -p /var/www/html/data && chown -R www-data:www-data /var/www/html/data && chmod -R 775 /var/www/html/data
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=3s --retries=3 CMD php -r "exit(@file_get_contents('http://localhost/api/data.php')?'0':'1');"
