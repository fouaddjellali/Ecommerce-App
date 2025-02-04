FROM php:8.2-fpm

# Installer les extensions PHP requises
RUN apt-get update && apt-get install -y \
	libpq-dev \
	unzip \
	git \
	&& docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/symfony

# Copier les fichiers du projet Symfony dans le conteneur
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-interaction --optimize-autoloader

CMD ["php-fpm"]
