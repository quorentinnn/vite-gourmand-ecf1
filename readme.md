# Vite & Gourmand

Projet ECF - Titre Professionnel Developpeur Web et Web Mobile (DWWM)
Candidat : Quorentin ROBICHON - Formation Studi - 2026

---

## Presentation du projet

Vite & Gourmand est un site web pour un traiteur fictif base a Bordeaux.
Les clients peuvent voir les menus, passer commande en ligne et suivre leurs livraisons.

Le site a 4 types d'utilisateurs :
- Visiteur : voir les menus, filtrer par categorie ou allergenes, creer un compte
- Client : commander, suivre ses commandes, laisser un avis, annuler une commande
- Employe : gerer les menus, uploader des images, traiter les commandes
- Admin : tout ce que fait l'employe + creer des comptes employes + voir les statistiques

---

## Technologies utilisees

- PHP 8.2 : langage backend
- MySQL : base de donnees relationnelle
- MongoDB : statistiques admin (NoSQL)
- Bootstrap 5 : design responsive
- JavaScript : filtres dynamiques
- Chart.js : graphiques dans le dashboard admin
- Railway : hebergement en ligne
- Git / GitHub : gestion du code

---

## Liens du projet

- Site en ligne : https://test-ecf-production.up.railway.app/
- Repository GitHub : https://github.com/quorentinnn/vite-gourmand-ecf1
- Maquettes Figma : https://www.figma.com/design/mG5GQQRyvTBPqQ65pfRajh/Projet-ECF-Traiteur-commande-en-ligne
- Gestion de projet Notion : https://www.notion.so/Liste-des-t-ches-du-projet-2cac16d409b4807b90bec21c45d2fe9c

---

## Comptes de test

| Role      | Email                      | Mot de passe |
|-----------|---------------------------|--------------|
| Admin     | admin@vite-gourmand.fr    | admin12345   |
| Employe   | julie@vite-gourmand.fr    | admin12345   |
| Client    | jean.dupont@email.fr      | admin12345   |

---

## Installation en local

### Ce qu'il faut installer avant

- XAMPP (contient PHP 8.2 + MySQL + Apache) : https://www.apachefriends.org/
- MongoDB : https://www.mongodb.com/try/download/community
- Git : https://git-scm.com/

### Etapes

**1. Cloner le projet**
```
git clone https://github.com/quorentinnn/vite-gourmand-ecf1.git
```

**2. Copier le dossier dans XAMPP**
```
Copier le dossier dans : C:/xampp/htdocs/vite-gourmand/
```

**3. Creer la base de donnees**
- Ouvrir phpMyAdmin : http://localhost/phpmyadmin
- Creer une base de donnees nommee "ecf_gourmand"
- Importer le fichier : database/ecf_gourmand.sql

**4. Modifier la connexion base de donnees**

Ouvrir le fichier includes/db.php et modifier si besoin :
```php
$host = 'localhost';
$dbname = 'ecf_gourmand';
$username = 'root';
$password = '';
```

**5. Lancer MongoDB**
```
mongod --dbpath C:/data/db
```

**6. Lancer le site**
```
Demarrer Apache et MySQL dans XAMPP
Aller sur : http://localhost/vite-gourmand/
```

---

## Deploiement sur Railway

Railway est la plateforme cloud que j'ai utilisee pour mettre le site en ligne.

### Comment ca fonctionne

Railway se connecte directement a GitHub. A chaque fois que je fais un push sur la branche main, Railway redeploit automatiquement le site. Il gere aussi les bases de donnees MySQL et MongoDB directement.

### Etapes pour deployer

**1. Creer un compte sur railway.app** et se connecter avec GitHub

**2. Creer un nouveau projet**
- Cliquer sur "New Project"
- Choisir "Deploy from GitHub repo"
- Selectionner le repository vite-gourmand-ecf1
- Railway detecte PHP et deploie automatiquement

**3. Ajouter MySQL**
- Dans le projet, cliquer "New Service" -> "Database" -> "MySQL"
- Railway cree les variables de connexion automatiquement

**4. Ajouter MongoDB**
- Cliquer "New Service" -> "Database" -> "MongoDB"
- Railway genere la variable MONGO_URL automatiquement

**5. Variables d'environnement**

Dans l'onglet Variables du service PHP, Railway fournit automatiquement :
```
MYSQLHOST
MYSQLPORT
MYSQLDATABASE
MYSQLUSER
MYSQLPASSWORD
MONGO_URL
```

**6. Fichier nixpacks.toml**

Ce fichier a la racine du projet sert a configurer le serveur Railway :
```toml
[phases.setup]
nixPkgs = ["php82", "php82Extensions.pdo", "php82Extensions.pdo_mysql", "php82Extensions.mongodb"]

[start]
cmd = "php -S 0.0.0.0:$PORT"
```

**7. Importer la base de donnees**

Apres le premier deploiement, importer les donnees via Railway CLI :
```
railway connect MySQL
source database/ecf_gourmand.sql
```

**8. Mise a jour du site**

Pour mettre a jour le site apres une modification :
```
git checkout main
git merge develop
git push origin main
```
Railway redeploit automatiquement en quelques minutes.

---

## Structure des dossiers

```
vite-gourmand-ecf1/
|
|-- public/         pages visiteurs et clients
|-- employe/        espace employe
|-- admin/          espace admin avec statistiques
|-- includes/       fichiers partages (connexion BDD, header, footer)
|-- database/       script SQL et schema MCD
|-- images/         images des menus
|-- CSS/            feuilles de style
|-- JS/             fichiers JavaScript
|-- nixpacks.toml   configuration Railway
|-- README.md       ce fichier
```

---

## Securite

- Injections SQL : utilisation de PDO avec requetes preparees
- XSS : htmlspecialchars() sur toutes les donnees affichees
- Mots de passe : hachage bcrypt avec password_hash()
- Sessions : verification du role sur chaque page protegee
- Upload images : verification du type MIME, taille max 5Mo, renommage unique
- HTTPS : fourni par Railway en production

---

## Base de donnees

Les fichiers sont dans le dossier database/ :
- ecf_gourmand.sql : script complet de creation de la base avec les donnees de test
- MCD_Vite_Gourmand.png : schema de la base de donnees

Tables principales : utilisateurs, menus, plats, commandes, avis, allergenes, themes, regimes, horaires
Tables de liaison : composer (menus-plats), provoquer (plats-allergenes)

---

Projet realise dans le cadre du titre professionnel DWWM - Studi 2026
