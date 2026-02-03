# Vite & Gourmand - Application de Traiteur

## Description du projet

**Vite & Gourmand** est une application web complète pour une entreprise de traiteur basée à Bordeaux. L'application permet aux clients de consulter les menus, passer des commandes en ligne, et aux employés/administrateurs de gérer l'ensemble des services.

**Projet réalisé dans le cadre de l'ECF (Évaluation en Cours de Formation)**

---

##  Technologies utilisées

### Backend
- **PHP 8.2** - Langage serveur
- **MySQL** - Base de données relationnelle (menus, utilisateurs, commandes)
- **MongoDB** - Base de données NoSQL (statistiques)
- **PDO** - Connexion sécurisée aux bases de données

### Frontend
- **HTML5 / CSS3**
- **Bootstrap 5.1.3** - Framework CSS
- **JavaScript Vanilla**
- **Chart.js** - Graphiques statistiques

### Outils
- **XAMPP** - Serveur local (Apache + MySQL)
- **MongoDB Compass** - Interface MongoDB
- **phpMyAdmin** - Gestion MySQL

---

##  Structure du projet
```
TEST_ECF/
│
├── admin/                          # Interface administrateur
│   ├── ajouter-menu.php
│   ├── modifier-menu.php
│   ├── supprimer-menu.php
│   ├── gestion-menus.php
│   ├── gestion-themes.php
│   ├── gestion-regimes.php
│   ├── gestion-allergenes.php
│   ├── gestion-plats.php
│   ├── gerer-employes.php
│   └── statistiques.php
│
├── employee/                       # Interface employé
│   ├── commandes.php
│   └── modifier-commande.php
│
├── public/                         # Pages publiques
│   ├── index.php
│   ├── menus.php
│   ├── menus-detail.php
│   ├── inscription.php
│   ├── connexion.php
│   ├── contact.php
│   ├── traiter-commande.php
│   ├── mon-compte.php
│   ├── annuler-commande.php
│   ├── ajouter-avis.php
│   ├── mentions-legales.php
│   ├── cgv.php
│   ├── mot-de-passe-oublie.php
│   └── reinitialiser-mot-de-passe.php
│
├── includes/                       # Fichiers partagés
│   ├── db.php                     # Connexion MySQL
│   ├── mongodb.php                # Fonctions MongoDB
│   ├── header.php                 # En-tête du site
│   ├── footer.php                 # Pied de page
│   └── messages.php               # Gestion des messages
│
├── uploads/                        # Images uploadées
│   └── [images des menus]
│
├── css/                           # Feuilles de style
│   └── style.css
│
└── README.md                      # Ce fichier
```

---

##  Installation

### Prérequis

- **XAMPP** (ou WAMP/MAMP) avec PHP 8.2+
- **MongoDB** installé et en cours d'exécution
- **Extension PHP MongoDB** activée
- Navigateur web moderne

### Étapes d'installation

#### 1. Cloner le projet
```bash
git clone [URL_DU_REPO]
cd TEST_ECF
```

#### 2. Configurer la base de données MySQL

Ouvrir **phpMyAdmin** et importer le fichier SQL :
- Base de données : `ecf_gourmand`
- Importer : `database/ecf_gourmand.sql`

Ou créer manuellement les tables selon le MCD fourni.

#### 3. Configurer MongoDB

Lancer MongoDB :
```bash
mongod
```

MongoDB créera automatiquement la base `ecf_gourmand` et la collection `statistiques_commandes`.

#### 4. Configurer la connexion

Modifier le fichier `/includes/db.php` si nécessaire :
```php
$host = 'localhost';
$dbname = 'ecf_gourmand';
$username = 'root';
$password = '';
```

#### 5. Installer l'extension MongoDB pour PHP

Si ce n'est pas déjà fait :

1. Télécharger le driver MongoDB pour PHP 8.2 (TS x64)
2. Copier `php_mongodb.dll` dans `C:\xampp\php\ext\`
3. Ajouter dans `php.ini` : `extension=mongodb`
4. Redémarrer Apache

#### 6. Créer le dossier uploads
```bash
mkdir uploads
chmod 755 uploads
```

#### 7. Accéder à l'application
```
http://localhost/TEST_ECF/public/index.php
```

---

##  Comptes de test

### Administrateur
- **Email** : admin@vitegourmand.fr
- **Mot de passe** : Admin123!

### Employé
- **Email** : employe@vitegourmand.fr
- **Mot de passe** : Employe123!

### Client
- **Email** : client@test.fr
- **Mot de passe** : Client123!

---

##  Fonctionnalités

### Pour les visiteurs
-  Consultation des menus avec filtres (prix, thème, régime)
-  Détail des menus avec composition et allergènes
-  Création de compte client
-  Contact

### Pour les clients (utilisateurs connectés)
-  Passer des commandes en ligne
-  Consulter l'historique de commandes
-  Annuler une commande (si statut = "en attente")
-  Laisser des avis sur les commandes livrées
- Modifier ses informations personnelles
-  Réinitialiser son mot de passe

### Pour les employés
-  Gérer les commandes (accepter, refuser, mettre à jour le statut)
-  CRUD complet sur les menus, plats, thèmes, régimes, allergènes
-  Valider les avis clients

### Pour les administrateurs
-  Toutes les fonctionnalités employé
-  Créer et désactiver des comptes employés
-  Visualiser les statistiques (graphiques)
  - Nombre de commandes par menu
  - Chiffre d'affaires par menu
  - Filtres par date et menu
- Gestion complète du contenu

### Fonctionnalités techniques
-  Authentification sécurisée (sessions PHP, mots de passe hashés)
-  Gestion des rôles (visiteur, client, employé, admin)
-  Upload d'images pour les menus
- Stockage des statistiques dans MongoDB
-  Calcul automatique des prix (réductions, livraison)
- Validation des données (côté client et serveur)
-  Protection contre les injections SQL (requêtes préparées)
-  Pages légales (Mentions légales, CGV)

---

## 🗄️ Base de données

### MySQL - Tables principales

- **utilisateurs** : Gestion des comptes (clients, employés, admin)
- **menus** : Menus proposés
- **plats** : Plats individuels (entrées, plats, desserts)
- **themes** : Thèmes des menus (Noël, Pâques, etc.)
- **regimes** : Régimes alimentaires (Végétarien, Vegan, etc.)
- **allergenes** : Liste des allergènes
- **commandes** : Commandes passées par les clients
- **avis** : Avis clients sur les commandes
- **composer** : Table de liaison (quels plats composent quel menu)
- **horaires** : Horaires d'ouverture

### MongoDB - Collections

- **statistiques_commandes** : Données pour les graphiques admin

---

##  Sécurité

- Mots de passe hashés avec `password_hash()` (bcrypt)
- Requêtes préparées PDO (protection SQL injection)
- Sessions sécurisées PHP
- Validation des données côté serveur
- Protection CSRF (tokens dans les formulaires critiques)
- Gestion des permissions par rôle

---

## Règles métier

### Commandes
- Une commande passe par plusieurs statuts :
  - `en_attente` → `accepté` → `en_préparation` → `en_cours_de_livraison` → `livré` → `terminée`
- Le client peut annuler tant que le statut = "en_attente"
- L'employé doit contacter le client avant d'annuler une commande

### Prix
- Prix de base du menu
- Réduction de 10% si commande ≥ 5 personnes au-dessus du minimum
- Frais de livraison : 5€ fixe (Bordeaux) + 0,59€/km (hors Bordeaux)

### Matériel
- Si matériel prêté : délai de retour 10 jours
- Pénalité de 600€ si non retourné

### Avis
- Un avis ne peut être laissé que si commande = "terminée"
- L'employé doit valider l'avis avant publication

---

## 🐛 Problèmes connus

Aucun problème majeur identifié à ce jour.

---

## Contact

**Auteur** : Quorentin  
**Projet** : ECF Développeur Web  
**Date** : Janvier 2026

---

## Licence

Ce projet est réalisé dans un cadre pédagogique.