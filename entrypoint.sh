#!/bin/bash
set -e

# Exécuter les migrations Doctrine avant de démarrer le serveur
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Démarrer Apache
exec apache2-foreground