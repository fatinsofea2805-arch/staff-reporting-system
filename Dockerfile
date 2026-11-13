FROM php:8.4-apache
RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY . /var/www/html/
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf
COPY deploy/php.ini /usr/local/etc/php/conf.d/staff.ini
COPY deploy/start.sh /usr/local/bin/start-staff
RUN printf 'Listen 10000\n' > /etc/apache2/ports.conf \
    && chmod +x /usr/local/bin/start-staff
ENV STAFF_DB_PATH=/var/data/ptjpm.sqlite
EXPOSE 10000
CMD ["start-staff"]
