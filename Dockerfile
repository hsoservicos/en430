# syntax=docker/dockerfile:1

# Sistema de Avaliação — Introdução à Enfermagem (EN_430)
# PHP 8.6 + Apache (mod_rewrite/.htaccess) + SQLite via PDO
#
# Contexto de build: a RAIZ do repositório.
#   docker build -t en430 .
FROM php:8.6-apache

# mbstring não vem compilado na imagem oficial 8.6 (exigido pelo composer/PHPUnit)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install mbstring \
    && rm -rf /var/lib/apt/lists/*

# Módulos do Apache necessários para o .htaccess do front controller
RUN a2enmod rewrite headers expires deflate

# AllowOverride All para que o .htaccess (roteamento, headers, cache) tenha efeito
# Endurecimento: sem versão/SO no header Server, sem assinatura em páginas de erro
RUN printf '%s\n' \
    'ServerTokens Prod' \
    'ServerSignature Off' \
    'TraceEnable Off' \
    '<Directory /var/www/html>' \
    '    Options -Indexes +FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/en430.conf \
    && a2enconf en430

# Hardening do PHP (expose_php, display_errors, sessão, upload_progress)
COPY sistema_avaliacao/php/docker/php-security.ini /usr/local/etc/php/conf.d/zz-en430-security.ini

# O security.conf do Debian é carregado depois do nosso conf e reativaria
# ServerTokens OS / ServerSignature On — ajustar na origem.
RUN sed -i 's/^ServerTokens OS/ServerTokens Prod/; s/^ServerSignature On/ServerSignature Off/' \
    /etc/apache2/conf-available/security.conf

WORKDIR /var/www/html

# Aplicação PHP (front controller, views, assets)
COPY sistema_avaliacao/php/ /var/www/html/

# Materiais de estudo servidos em /materiais/ (HTML + PDF)
COPY materiais/ /var/www/html/materiais/

# Política de Privacidade exibida em /politica-de-privacidade
# (o .htaccess bloqueia .md; o arquivo é lido pelo PHP, não servido diretamente)
COPY docs/juridico/POLITICA_DE_PRIVACIDADE.md /var/www/html/politica.md

# SQLite persistente (montado como volume do Coolify em /data)
ENV APP_DB_PATH=/data/avaliacao.db
RUN mkdir -p /data /var/www/html/backups \
    && chown -R www-data:www-data /data /var/www/html

COPY sistema_avaliacao/php/docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod 0755 /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD php -r '$r = @file_get_contents("http://127.0.0.1/index.php"); exit($r === false ? 1 : 0);'

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
