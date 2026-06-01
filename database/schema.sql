-- Base de données Vite & Gourmand
-- Script de création des tables

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client', 'employe', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE regimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE allergenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    nb_personnes_min INT DEFAULT 1,
    stock INT DEFAULT 0,
    theme_id INT,
    regime_id INT,
    FOREIGN KEY (theme_id) REFERENCES themes(id),
    FOREIGN KEY (regime_id) REFERENCES regimes(id)
);

CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,
    nb_personnes INT NOT NULL,
    date_livraison DATE NOT NULL,
    heure_livraison TIME,
    adresse_livraison VARCHAR(255),
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    prix_total DECIMAL(10,2),
    statut ENUM('en_attente','acceptee','en_preparation','en_livraison','livree','annulee') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    commande_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    valide TINYINT DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id),
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
);
