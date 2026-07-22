FROM php:8.3-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip intl

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Configuration Apache : pointer le DocumentRoot vers public/
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        FallbackResource /index.php\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tout le projet
COPY . .

# IMPORTANT : forcer l'environnement en production avant l'installation
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer les dépendances PHP (sans les paquets de dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Vider et réchauffer le cache manuellement en mode prod
RUN php bin/console cache:clear --env=prod --no-debug

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html/var

# Copier et rendre exécutable le script d'entrée
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh && \
    sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]