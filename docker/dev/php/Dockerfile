FROM php:8.4-fpm

RUN DEBIAN_FRONTEND=noninteractive apt update -y && \
    apt install -y \
    libxml2-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgif-dev \
    libavif-dev \
    libwebp-dev

# Set the development php.ini to default
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Remove unused files from php directory
RUN rm -rf -r /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini-production

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-avif \
    --with-webp && \
    docker-php-ext-install pdo_mysql intl exif curl zip -j$(nproc) gd dom && \
    docker-php-ext-enable pdo_mysql intl exif curl zip gd dom

# Copy the php fpm docker configuration with a different exposed port
# which should be the port being exposed in the end of this file
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 9000

CMD ["php-fpm", "-F"]