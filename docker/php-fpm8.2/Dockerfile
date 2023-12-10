FROM php:8.2.0-fpm-alpine

# Install packages
RUN apk add --update --no-cache \
    curl \
    git \
    build-base \
    zlib-dev \
    oniguruma-dev \
    autoconf \
    bash \
    linux-headers \
    icu-dev

# Configure non-root user.
ARG UID
ARG GID
ARG USER
ARG GROUP

RUN addgroup -g ${GID} -S ${GROUP} \
    && adduser -u ${UID} -S -D ${USER} ${GROUP}

# Workdir
WORKDIR /var/www

# Config file
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && echo "alias composer='XDEBUG_MODE=off composer'" >> /home/app/.bashrc

# Remove docker config file
RUN rm /usr/local/etc/php-fpm.d/zz-docker.conf

# Install symfony cli
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install php extensions
RUN docker-php-ext-install \
    intl \
    opcache \
    pdo_mysql

# Xdebug
ARG INSTALL_XDEBUG=false
ARG HTTP_PROXY=false

RUN if [ ${HTTP_PROXY} != false ]; then pear config-set http_proxy ${HTTP_PROXY}; fi

RUN if [ ${INSTALL_XDEBUG} = true ]; then pecl install xdebug; fi;

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN if [ ${INSTALL_XDEBUG} != true ]; then rm /usr/local/etc/php/conf.d/xdebug.ini; fi