# Frigo App

Projet réalisé dans le cadre du cours **IFT3225 – Technologies des applications Web** à l'Université de Montréal.

**Auteurs :** Lea Hemidj, Youssef

---

## Description

Frigo App est une application web qui permet de gérer les ingrédients de son réfrigérateur et de découvrir des recettes adaptées à ce qu'on a sous la main. L'idée de départ était de réduire le gaspillage alimentaire en ayant une vue claire sur ce qui expire bientôt.

L'application a été déployée sur les serveurs de l'université (`www-ens.iro.umontreal.ca`).

---

## Fonctionnalités

- **Gestion des ingrédients** : ajouter, modifier et supprimer des ingrédients avec leur quantité, catégorie et date de péremption
- **Filtres** : recherche par nom, filtre par catégorie, filtre par statut d'expiration (expirant bientôt / déjà expiré)
- **Gestion des recettes** : créer ses propres recettes en listant les ingrédients nécessaires
- **Correspondance recettes/frigo** : les recettes dont tous les ingrédients sont disponibles et non périmés sont mises en surbrillance
- **Recette aléatoire** : tirage au sort parmi les recettes enregistrées
- **Authentification** : inscription, connexion, déconnexion — chaque utilisateur gère ses propres données
- **Rôle admin** : compte administrateur avec accès étendu

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| UI | Bootstrap 5.3 |
| Backend | PHP 8 |
| Base de données | MySQL / MariaDB |
| Hébergement | Serveurs de l'Université de Montréal |

---

## Structure du projet

```
frigo app/
├── frigo.php              # Page principale (interface utilisateur)
├── index.php              # Redirection vers frigo.php
├── app.js                 # Toute la logique frontend (fetch, rendu, pagination)
├── style.css              # Styles personnalisés
│
├── login.php              # API : connexion
├── register.php           # API : inscription
├── logout.php             # Déconnexion (destroy session)
│
├── get_ingredients.php    # API : liste des ingrédients de l'utilisateur
├── add_ingredient.php     # API : ajout d'un ingrédient
├── update_ingredient.php  # API : modification d'un ingrédient
├── delete_ingredient.php  # API : suppression d'un ingrédient
│
├── get_recipe.php         # API : liste des recettes de l'utilisateur
├── add_recipe.php         # API : ajout d'une recette
├── update_recipe.php      # API : modification d'une recette
├── delete_recipe.php      # API : suppression d'une recette
│
├── db.php.example         # Template de configuration de la base de données
├── db_schema.sql          # Schéma de la base (tables users, ingredients, recipes)
└── seed.sql               # Données de démonstration
```

---

## Installation locale

### Prérequis

- PHP 8+
- MySQL 5.7+ ou MariaDB 10.2+
- Un serveur web local (MAMP, XAMPP, Laragon…)

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/<votre-utilisateur>/frigo-app.git
   ```

2. **Créer la base de données**
   ```bash
   mysql -u root -p < db_schema.sql
   ```

3. **Configurer la connexion**
   ```bash
   cp db.php.example db.php
   # Éditer db.php avec vos identifiants MySQL
   ```

4. **Charger les données de démo** *(optionnel)*
   ```bash
   mysql -u root -p < seed.sql
   ```

5. Ouvrir `http://localhost/frigo-app/frigo.php` dans le navigateur.

### Compte de démo (après seed.sql)

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@local | admin123 | admin |

---

## Déploiement sur les serveurs de l'université

L'application a été déployée sur `www-ens.iro.umontreal.ca` en copiant les fichiers PHP dans le répertoire web associé au compte étudiant. La base de données MySQL fournie par l'université a été utilisée (base `hemidjle_frigo`, utilisateur `hemidjle`).

Le fichier `db.php` a été configuré directement sur le serveur et n'est pas versionné (voir `.gitignore`).

---

## Aperçu

- Section **Accueil** : présentation et accès rapide
- Section **Ingrédients** : tuiles colorées par catégorie avec badge d'expiration
- Section **Recettes** : cartes recettes, mise en surbrillance si tous les ingrédients sont dispo
- Section **Connexion** : formulaire inline avec ouverture de modale pour l'inscription
