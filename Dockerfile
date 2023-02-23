# FROM dahirmuhammaddahir/secureng-php-apache
FROM kooldev/php:7.4-nginx

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS
RUN apk add linux-headers
RUN echo "#include <unistd.h>" > /usr/include/sys/unistd.h

RUN set -xe \
    # Download the desired package(s)
    && curl -O https://download.microsoft.com/download/e/4/e/e4e67866-dffd-428c-aac7-8d28ddafb39b/msodbcsql17_17.10.2.1-1_amd64.apk \
    && curl -O https://download.microsoft.com/download/e/4/e/e4e67866-dffd-428c-aac7-8d28ddafb39b/mssql-tools_17.10.1.1-1_amd64.apk \
    #Install the package(s)
    && apk add --allow-untrusted msodbcsql17_17.10.2.1-1_amd64.apk \
    && apk add --allow-untrusted mssql-tools_17.10.1.1-1_amd64.apk \
    && apk add --no-cache --virtual .persistent-deps freetds unixodbc \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS unixodbc-dev freetds-dev \
    && docker-php-source extract \
    && docker-php-ext-install pdo_dblib \
    && docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr \
    && docker-php-ext-install pdo_odbc \
    && pecl install sqlsrv pdo_sqlsrv \
    && echo extension=pdo_sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/10_pdo_sqlsrv.ini \
    && echo extension=sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/00_sqlsrv.ini \
    # && docker-php-ext-enable --ini-name 30-sqlsrv.ini sqlsrv \
    # && docker-php-ext-enable --ini-name 35-pdo_sqlsrv.ini pdo_sqlsrv \
    && docker-php-source delete \
    && apk del .build-deps

RUN apk add --no-cache --update --virtual buildDeps autoconf
RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS}
RUN pecl install -f xdebug-3.1.6
RUN docker-php-ext-enable xdebug 
RUN apk del pcre-dev ${PHPIZE_DEPS}
RUN apk del buildDeps

RUN curl -sS https://getcomposer.org/installer​ | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install grpc-1.35.0

WORKDIR /app/public

COPY . .

RUN composer update && \
    composer install && \
    cd thirdparty/grpc-fingerprint-engine/src/php/ && \
    composer update && \
    composer install

