FROM php:7.1.8-fpm

#dependencies
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
        git \
        wget \
        vim \
        libpq-dev \
        libicu-dev \
        zlib1g-dev \
        mysql-server \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        libpcre3-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install intl mbstring mysqli pdo pdo_mysql zip gd \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

#gosu
RUN apt-get install ca-certificates
RUN gpg --keyserver ha.pool.sks-keyservers.net --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4
RUN curl -L -o /usr/local/bin/gosu -SL "https://github.com/tianon/gosu/releases/download/1.4/gosu-$(dpkg --print-architecture)" \
    && curl -L -o /usr/local/bin/gosu.asc -SL "https://github.com/tianon/gosu/releases/download/1.4/gosu-$(dpkg --print-architecture).asc" \
    && gpg --verify /usr/local/bin/gosu.asc \
    && rm /usr/local/bin/gosu.asc \
    && chmod +x /usr/local/bin/gosu

#xdebug
RUN pecl install -o -f xdebug-2.5.4 \
    && rm -rf /tmp/pear

#configuration
COPY php.ini /usr/local/etc/php/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir -p /var/www/html
WORKDIR /var/www/html/

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

