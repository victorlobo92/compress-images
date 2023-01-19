FROM composer:2 as composer_stage
 
RUN rm -rf /var/www && mkdir -p /var/www/html
WORKDIR /var/www/html
 
FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libwebp-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install -j$(nproc) gd
 
WORKDIR /var/www/html
COPY app ./


COPY --from=composer_stage /usr/bin/composer /usr/bin/composer
 
CMD ["php-fpm"]
 
EXPOSE 9000