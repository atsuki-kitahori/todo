FROM php:8.1-fpm-alpine
COPY php.ini /usr/local/etc/php/
RUN apk update \ 
	&& docker-php-ext-install pdo_mysql