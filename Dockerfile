FROM php:8.2-cli

RUN rm -f /ruta/a/tu/cache.json

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html
COPY . .

EXPOSE 3000

CMD ["php", "-S", "0.0.0.0:3000", "-t", "public"]
