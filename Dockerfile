FROM dahirmuhammaddahir/secureng-php-apache

ENV ACCEPT_EULA=Y
RUN apt-get update && apt-get install -y gnupg2
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - 
RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list 
RUN apt-get update 
RUN ACCEPT_EULA=Y apt-get -y --no-install-recommends install msodbcsql17 unixodbc-dev 
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

WORKDIR /var/www/html

COPY . .

RUN composer update && \
    composer install && \
    cd thirdparty/grpc-fingerprint-engine/src/php/ && \
    composer update && \
    composer install && \
    apt update && apt install -y net-tools

COPY ./openssl.cnf /etc/ssl/openssl.cnf

# https://github.com/microsoft/msphpsql/issues/1112#issuecomment-643522139