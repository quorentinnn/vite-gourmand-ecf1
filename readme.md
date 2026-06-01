# Vite & Gourmand - Site de Traiteur

Application web pour un traiteur à Bordeaux. Projet réalisé pour l'ECF Développeur Web et Web Mobile.

## 🚀 Démo

- **Site en ligne** : https://test-ecf-production.up.railway.app
- **GitHub** : https://github.com/quorentinnn/vite-gourmand-ecf1

## 🔐 Comptes de test

**Admin** : admin@vitegourmand.fr / Admin12345  
**Employé** : julie@vite-gourmand.fr / Employe12345

## 📋 Prérequis

- XAMPP avec PHP 8.0+
- MongoDB
- Navigateur web

## 📥 Installation

### 1. Cloner le projet
```bash
git clone https://github.com/quorentinnn/vite-gourmand-ecf1.git
```

### 2. Mettre dans XAMPP
Copier le dossier dans :
```
C:\xampp\htdocs\vite-gourmand-ecf1\
```

### 3. Créer la base MySQL
1. Ouvrir phpMyAdmin : http://localhost/phpmyadmin
2. Créer une base : `ecf_gourmand`
3. Importer le fichier SQL (à créer en exportant ta base)

### 4. Lancer MongoDB
```bash
mongod
```

### 5. Ouvrir le site
```
http://localhost/vite-gourmand-ecf1/public/
```

## 🛠️ Technologies

**Backend :** PHP 8.2, MySQL, MongoDB  
**Frontend :** HTML, CSS, Bootstrap 5, JavaScript  
**Outils :** XAMPP, Git, Railway

## 📁 Structure
```
├── admin/          # Pages administrateur
├── employe/        # Pages employé
├── public/         # Pages publiques
├── includes/       # Fichiers partagés (db.php, header.php, etc.)
├── CSS/            # Styles
├── JS/             # Scripts
├── uploads/        # Images uploadées
└── README.md
```

## ✨ Fonctionnalités

**Visiteur :**
- Voir les menus
- Filtrer par prix, catégorie, allergènes
- S'inscrire

**Client :**
- Commander des menus
- Suivre ses commandes
- Laisser des avis

**Employé :**
- Gérer les menus et plats
- Traiter les commandes
- Valider les avis

**Admin :**
- Tout ce que fait l'employé
- Créer des comptes employés
- Voir les statistiques (Chart.js + MongoDB)

## 🔒 Sécurité

- Mots de passe hashés (bcrypt)
- Requêtes préparées PDO
- Protection XSS avec htmlspecialchars()
- Sessions sécurisées

## 👤 Auteur

Quentin - ECF Développeur Web et Web Mobile  
Février 2026