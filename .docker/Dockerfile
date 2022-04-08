FROM leandrosilva/php5.6-apache

MAINTAINER Sanya Thammawong

COPY . /var/www/html

RUN chown -R www-data:www-data /src/app && a2enmod rewrite

EXPOSE 80/tcp