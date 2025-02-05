📌 E-Commerce Portes

Bienvenue sur le projet de site e-commerce dédié à la vente de portes. Ce projet est développé avec Symfony et utilise Doctrine ORM pour la gestion de la base de données.

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone https://github.com/fouaddjellali/Ecommerce-App.git
cd Ecommerce-App/
```

### 2. Lancer les services Docker
Assurez-vous d'avoir Docker et Docker Compose installés sur votre machine.
```bash
docker compose up -d
```

### 3. Accéder au conteneur PHP
```bash
docker compose exec php bash
```

### 4. Configurer la base de données
Créer le schéma de la base de données :
```bash
php bin/console doctrine:migrations:migrate
```

Charger les données de test :
```bash
php bin/console doctrine:fixtures:load
```

## 🖥️ Accès au site
Une fois les services lancés, ouvrez votre navigateur et accédez à :
```
http://localhost
```

## 🛠 Technologies utilisées
- Symfony
- Doctrine ORM
- Docker & Docker Compose
- PostgreSQL (via Docker)