# syntax=docker/dockerfile:1

# Sistema de Avaliação — Introdução à Enfermagem (EN_430)
# PHP 8.4 + Apache (mod_rewrite/.htaccess) + SQLite via PDO
FROM php:8.4-apache

# Módulos do Apache necessários para o .htaccess do front controller
RUN a2enmod rewrite headers expires deflate

# AllowOverride All para que o .htaccess (roteamento, headers, cache) tenha efeito
RUN printf '%s\n' \
    '<Directory /var/www/html>' \
    '    Options -Indexes +FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/en430.conf \
    && a2enconf en430

WORKDIR /var/www/html

COPY . .

# SQLite persistente (montado como volume do Coolify em /data)
ENV APP_DB_PATH=/data/avaliacao.db
RUN mkdir -p /data /var/www/html/backups \
    && chown -R www-data:www-data /data /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 0755 /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD php -r '$r = @file_get_contents("http://127.0.0.1/index.php"); exit($r === false ? 1 : 0);'

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
