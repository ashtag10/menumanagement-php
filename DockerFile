# Utilise l'image de base PHP (FastCGI Process Manager)
FROM php:8.2-fpm-alpine

# Installe les dépendances nécessaires à la compilation (build-deps)
# et les paquets de développement PostgreSQL (postgresql-dev) pour libpq-fe.h.
WORKDIR /app

# [MODIFICATION CLÉ : Ajout de build-deps et postgresql-dev]
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apk del .build-deps


# ÉTAPE CRUCIALE 1 : Copie des fichiers de configuration Composer en premier
COPY composer.json composer.lock ./

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installe les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Copie le code de l'application dans le conteneur
COPY . /app

# Expose le port par défaut (Render utilisera $PORT mais c'est une bonne pratique)
EXPOSE 8080

# CRITIQUE : Définit la commande de démarrage (serveur PHP intégré)
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public/"]
