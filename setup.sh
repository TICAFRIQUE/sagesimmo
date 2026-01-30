#!/bin/bash

echo "🚀 Initialisation du projet Laravel..."

# Vérifier que Composer est installé
if ! command -v composer &> /dev/null
then
    echo "❌ Composer n'est pas installé"
    exit
fi

# Installation des dépendances
echo "📦 Installation des dépendances Composer..."
composer install

# Copier le fichier .env
if [ ! -f .env ]; then
    echo "📄 Création du fichier .env"
    cp .env.example .env
fi

# Générer la clé
echo "🔑 Génération de la clé de l'application..."
php artisan key:generate

# Migration de la base de données
echo "🗄️ Migration de la base de données..."
php artisan migrate --force

# Seeder
echo "🌱 Exécution des seeders..."
php artisan db:seed --force

# Cache config
echo "⚡ Optimisation..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

echo "✅ Projet prêt !"

# Exécuter le serveur
echo "🚀 Exécuter le serveur..."
php artisan serve 