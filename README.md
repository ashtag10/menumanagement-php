CRUD PHP avec PostgreSQL

Bienvenue dans le projet CRUD (Create, Read, Update, Delete) développé en PHP en utilisant une architecture MVC simple. Ce projet se distingue par sa capacité à gérer des données structurées (listes d'ingrédients ou autres) sous forme de JSON stocké dans une colonne PostgreSQL.

 *******Fonctionnalités Principales

Gestion des Items : Création, lecture, modification et suppression d'enregistrements (items).

Architecture MVC : Séparation claire entre les modèles (Models), les vues (Views), et les contrôleurs (Controllers).

Gestion des Ingrédients : Système JavaScript asynchrone pour ajouter et gérer une liste d'ingrédients dynamiquement dans le formulaire.

Base de Données : Utilisation de PostgreSQL avec PDO pour une gestion robuste des données.

 ********Configuration Locale (Développement)

1. Prérequis

Assurez-vous d'avoir installé :

PHP (version 7.4 ou supérieure recommandée)

PostgreSQL

Un client de base de données (pgAdmin ou psql)

2. Base de Données

Ce projet utilise les variables d'environnement pour se connecter à la base de données.

Fichier de Configuration .env :

Le fichier .env doit contenir vos identifiants de connexion. Lors du développement local, vous pouvez pointer vers votre base de données en ligne (Render) pour travailler directement avec les données déployées :

DB_CONNECTION=pgsql
DB_HOST=dpg-d3pi0nc9c44c73c26nug-a.oregon-postgres.render.com  # Hôte complet de Render
DB_PORT=5432
DB_DATABASE=crud_php 
DB_USERNAME=joanhack
DB_PASSWORD=VOTRE_MOT_DE_PASSE_FOURNI_PAR_RENDER 


3. Schéma de la Base de Données

La table principale requise est items.

Table : items

Colonne clé : ingredients (Type TEXT pour le stockage du JSON sérialisé).

Le schéma exécuté sur PostgreSQL est :

CREATE TABLE IF NOT EXISTS items (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients TEXT DEFAULT '[]' NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);


4. Configuration des Routes

Le fichier .htaccess gère la réécriture d'URL pour pointer toutes les requêtes vers le fichier index.php racine, permettant l'architecture MVC :

# Configuration Apache pour l'URL Rewriting
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]


 *******Déploiement (GitHub & Render)

Le déploiement est effectué en utilisant GitHub pour la gestion du code source et Render comme plateforme d'hébergement.

Étape 1 : Migration de la Base de Données

Création DB Render : Créer une base de données PostgreSQL sur Render (plan gratuit).

Export Local : Sauvegarder la base de données locale (schéma + données) en utilisant pgAdmin (Format Plain).

Import Render : Restaurer la sauvegarde locale sur la base de données Render via pgAdmin (en utilisant l'option "Clean before restore").

Rappel important : L'hôte de connexion doit être le FQDN complet (ex: dpg-...render.com), et non l'alias court parfois affiché.

Étape 2 : Déploiement du Service Web

GitHub : Le code source complet est poussé vers un dépôt GitHub.

Service Web Render : Créer un nouveau Web Service sur Render, lié au dépôt GitHub.

Configuration du Runtime :

Runtime : PHP

Start Command (Critique) : php -S 0.0.0.0:$PORT -t public/

Ceci garantit que l'application démarre et que le répertoire public/ (où se trouve l'index.php) est la racine du serveur web.

Étape 3 : Configuration des Variables d'Environnement (Secrets)

Pour que l'application en ligne se connecte à la base de données en ligne, les identifiants de connexion doivent être ajoutés dans les Environment Variables du service web Render :

Clé

Valeur

DB_HOST

Hôte complet de Render

DB_PORT

5432

DB_DATABASE

Nom de la base de données Render

DB_USERNAME

Utilisateur Render

DB_PASSWORD

Mot de passe Render

DB_CONNECTION

pgsql
