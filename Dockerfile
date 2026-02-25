FROM php:8.2-apache


RUN a2enmod rewrite


WORKDIR /var/www/html


COPY . .


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html


RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/project.conf \
    && a2enconf project

EXPOSE 80
