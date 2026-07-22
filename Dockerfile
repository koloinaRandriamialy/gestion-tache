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

# Configurer Apache pour pointer vers le dossier public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html/var

EXPOSE 80

CMD ["apache2-foreground"]