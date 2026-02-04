-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 03 fév. 2026 à 23:23
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecf_gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `allergenes`
--

CREATE TABLE `allergenes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `allergenes`
--

INSERT INTO `allergenes` (`id`, `nom`) VALUES
(4, 'Arachides'),
(9, 'Céleri'),
(7, 'Crustacés'),
(5, 'Fruits à coque'),
(1, 'Gluten'),
(3, 'Lait'),
(10, 'Moutarde'),
(2, 'Œuf'),
(6, 'Poisson'),
(8, 'Soja');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `note` int(11) NOT NULL CHECK (`note` >= 1 and `note` <= 5),
  `commentaire` text NOT NULL,
  `valide` tinyint(1) DEFAULT 0,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `utilisateur_id`, `commande_id`, `note`, `commentaire`, `valide`, `cree_le`) VALUES
(1, 4, 3, 5, 'Excellent menu pour notre réveillon ! Les plats étaient délicieux et la présentation soignée. Julie et José sont très professionnels. Je recommande vivement !', 1, '2026-01-12 11:03:03'),
(2, 5, 6, 4, 'Très bon menu végétarien, les légumes étaient frais et bien cuisinés. Seul petit bémol : la livraison avec 15 minutes de retard.', 1, '2026-01-12 11:03:03'),
(3, 4, 1, 5, 'Toujours un plaisir de commander chez Vite & Gourmand. Le Menu Midi est parfait pour nos déjeuners d\'équipe !', 0, '2026-01-12 11:03:03'),
(4, 6, 4, 3, 'Bon dans l\'ensemble, mais j\'ai trouvé les portions un peu justes pour le prix. Le goût était au rendez-vous néanmoins.', 1, '2026-01-12 11:03:03'),
(5, 12, 18, 4, 'FFFDDFDFDFD', 1, '2026-01-21 09:55:11'),
(6, 12, 18, 3, 'sss', 1, '2026-01-21 09:56:18'),
(7, 12, 18, 2, 'DDD', 1, '2026-01-22 10:16:05');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `nb_personnes` int(11) NOT NULL,
  `date_livraison` datetime NOT NULL,
  `heure_livraison` time NOT NULL,
  `adresse_livraison` text NOT NULL,
  `ville_livraison` varchar(100) NOT NULL,
  `code_postal_livraison` varchar(10) NOT NULL,
  `instructions` text DEFAULT NULL,
  `prix_menu` decimal(10,2) NOT NULL,
  `prix_livraison` decimal(10,2) NOT NULL,
  `reduction` decimal(10,2) DEFAULT 0.00,
  `prix_total` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','acceptee','en_preparation','en_livraison','livree','attente_retour_materiel','terminee') DEFAULT 'en_attente',
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_le` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `menu_id`, `nb_personnes`, `date_livraison`, `heure_livraison`, `adresse_livraison`, `ville_livraison`, `code_postal_livraison`, `instructions`, `prix_menu`, `prix_livraison`, `reduction`, `prix_total`, `statut`, `cree_le`, `modifie_le`) VALUES
(1, 4, 1, 6, '2026-01-20 12:00:00', '12:00:00', '123 Rue Exemple', 'Bordeaux', '33000', 'Code portail: 1234', 22.50, 5.00, 2.75, 24.75, '', '2026-01-12 10:50:41', '2026-01-20 10:25:21'),
(2, 5, 4, 8, '2026-01-25 19:00:00', '19:00:00', '45 Avenue Test', 'Bordeaux', '33200', NULL, 46.67, 5.00, 5.17, 46.50, 'acceptee', '2026-01-12 10:50:41', '2026-01-12 10:50:41'),
(3, 4, 5, 10, '2025-12-24 18:00:00', '18:00:00', '123 Rue Exemple', 'Bordeaux', '33000', 'Livraison pour le réveillon', 56.25, 5.00, 6.13, 55.12, 'terminee', '2026-01-12 10:50:41', '2026-01-12 10:50:41'),
(4, 6, 2, 4, '2026-01-15 12:30:00', '12:30:00', '78 Boulevard Exemple', 'Talence', '33400', NULL, 20.00, 6.77, 0.00, 26.77, 'en_livraison', '2026-01-12 10:50:41', '2026-01-12 10:50:41'),
(5, 7, 3, 6, '2026-01-05 19:00:00', '19:00:00', '12 Place du Marché', 'Mérignac', '33700', NULL, 33.00, 7.13, 3.30, 36.83, 'attente_retour_materiel', '2026-01-12 10:50:41', '2026-01-12 10:50:41'),
(6, 5, 9, 4, '2025-12-15 12:00:00', '12:00:00', '45 Avenue Test', 'Bordeaux', '33200', NULL, 18.00, 5.00, 0.00, 23.00, 'terminee', '2026-01-12 10:50:41', '2026-01-12 10:50:41'),
(7, 4, 8, 4, '2026-01-30 13:00:00', '13:00:00', '123 Rue Exemple', 'Bordeaux', '33000', NULL, 18.00, 5.00, 0.00, 23.00, '', '2026-01-12 10:50:41', '2026-01-20 10:25:30'),
(8, 1, 2, 5, '2026-01-09 00:00:00', '10:17:00', '', '', '', NULL, 0.00, 0.00, 0.00, 50.00, 'en_attente', '2026-01-16 09:41:53', '2026-01-16 09:41:53'),
(9, 1, 2, 30, '2026-01-09 00:00:00', '10:17:00', '', '', '', NULL, 0.00, 0.00, 0.00, 300.00, 'en_attente', '2026-01-16 09:42:37', '2026-01-16 09:42:37'),
(10, 1, 8, 6, '2026-01-17 00:00:00', '17:43:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, 'livree', '2026-01-16 16:40:38', '2026-01-20 10:36:06'),
(11, 1, 8, 6, '2026-01-17 00:00:00', '17:43:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, '', '2026-01-16 16:43:54', '2026-01-20 10:25:17'),
(12, 1, 8, 6, '2026-01-17 00:00:00', '17:43:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, '', '2026-01-16 16:44:44', '2026-01-20 10:25:13'),
(13, 1, 8, 6, '2026-01-17 00:00:00', '17:43:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, '', '2026-01-16 16:47:28', '2026-01-20 10:25:07'),
(14, 1, 8, 6, '2026-01-17 00:00:00', '17:43:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, '', '2026-01-16 16:49:01', '2026-01-20 10:25:06'),
(15, 2, 9, 6, '2026-01-17 00:00:00', '18:12:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 108.00, '', '2026-01-16 17:11:27', '2026-01-20 10:14:55'),
(16, 9, 9, 30, '2026-01-29 00:00:00', '18:47:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 540.00, '', '2026-01-17 17:44:14', '2026-01-20 10:14:52'),
(17, 11, 10, 30, '2026-01-29 00:00:00', '12:22:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 600.00, 'terminee', '2026-01-20 11:19:57', '2026-01-20 11:20:23'),
(18, 12, 9, 30, '2026-01-29 00:00:00', '10:51:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 540.00, 'livree', '2026-01-21 09:49:12', '2026-01-21 09:54:08'),
(19, 15, 16, 30, '2026-01-29 00:00:00', '17:06:00', '6 rue baptiste lasne', 'Sermaise', '49140', NULL, 0.00, 0.00, 0.00, 330.00, 'livree', '2026-01-27 16:01:05', '2026-01-27 16:53:18'),
(20, 12, 10, 6, '2026-01-02 00:00:00', '22:54:00', '30 boulevard Victor Hugo', 'Marseille', '13001', NULL, 0.00, 0.00, 0.00, 120.00, 'terminee', '2026-01-27 16:50:01', '2026-01-27 16:50:05'),
(21, 12, 16, 6, '2026-01-02 00:00:00', '00:22:00', '30 boulevard Victor Hugo', 'Marseille', '13001', NULL, 0.00, 0.00, 0.00, 66.00, 'en_attente', '2026-01-27 23:18:24', '2026-01-27 23:18:24'),
(22, 12, 5, 13, '2026-01-02 00:00:00', '03:30:00', '30 boulevard Victor Hugo', 'Marseille', '13001', NULL, 0.00, 0.00, 0.00, 585.00, 'en_attente', '2026-01-27 23:30:46', '2026-01-27 23:30:46');

-- --------------------------------------------------------

--
-- Structure de la table `composer`
--

CREATE TABLE `composer` (
  `menu_id` int(11) NOT NULL,
  `plat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `composer`
--

INSERT INTO `composer` (`menu_id`, `plat_id`) VALUES
(1, 1),
(1, 9),
(1, 21),
(2, 6),
(2, 19),
(2, 22),
(3, 4),
(3, 10),
(3, 23),
(4, 5),
(4, 13),
(4, 26),
(5, 3),
(5, 20),
(5, 27),
(8, 1),
(8, 10),
(8, 28),
(9, 4),
(9, 17),
(9, 21),
(10, 6),
(10, 17),
(10, 28);

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

CREATE TABLE `horaires` (
  `id` int(11) NOT NULL,
  `jour` varchar(20) NOT NULL,
  `heure_ouverture` time NOT NULL,
  `heure_fermeture` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `horaires`
--

INSERT INTO `horaires` (`id`, `jour`, `heure_ouverture`, `heure_fermeture`) VALUES
(1, 'Lundi', '09:00:00', '19:00:00'),
(2, 'Mardi', '09:00:00', '19:00:00'),
(3, 'Mercredi', '09:00:00', '19:00:00'),
(4, 'Jeudi', '09:00:00', '19:00:00'),
(5, 'Vendredi', '09:00:00', '19:00:00'),
(6, 'Samedi', '10:00:00', '18:00:00'),
(7, 'Dimanche', '00:00:00', '00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `nb_personnes_min` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `theme_id` int(11) NOT NULL,
  `regime_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id`, `titre`, `description`, `prix`, `nb_personnes_min`, `stock`, `theme_id`, `regime_id`, `image`, `cree_le`) VALUES
(1, 'Menu Midi', 'Un menu équilibré et savoureux parfait pour vos déjeuners d\'affaires ou repas en famille. Composé d\'une entrée fraîche, d\'un plat généreux et d\'un dessert gourmand.', 15.00, 4, 15, 1, 1, 'menu_midi.jpg', '2026-01-12 10:20:03'),
(2, 'Menu Enfant', 'Des plats adaptés aux plus jeunes avec des portions ajustées et des saveurs douces. Idéal pour les anniversaires et repas de famille.', 9.94, 2, 20, 5, 1, '6973348ae4b67.jpg', '2026-01-12 10:20:03'),
(3, 'Menu Soir', 'Un menu raffiné pour vos dîners en famille ou entre amis. Des saveurs authentiques et des produits de saison.', 22.00, 4, 12, 1, 1, 'menu_soir.jpg', '2026-01-12 10:20:03'),
(4, 'Menu Gastronomique', 'Une expérience culinaire raffinée avec des produits d\'exception pour vos grandes occasions. Service sur table et présentation soignée.', 34.67, 6, 8, 5, 1, 'menu_gastronomique.jpg', '2026-01-12 10:20:03'),
(5, 'Menu Spécial Noël', 'Célébrez les fêtes avec notre menu traditionnel revisité aux saveurs festives. Produits nobles et préparations raffinées.', 45.00, 8, 10, 2, 1, 'menu_noel.jpg', '2026-01-12 10:20:03'),
(8, 'Menu Été', 'Fraîcheur et légèreté pour vos repas estivaux. Salades composées et grillades savoureuses.', 0.80, 4, 15, 4, 1, 'menu_ete.jpg', '2026-01-12 10:20:03'),
(9, 'Menu Végétarien', 'Des créations savoureuses et équilibrées 100% végétariennes pour tous les goûts. Produits bio et locaux.', 18.00, 4, 12, 6, 2, 'menu_vegetarien.jpg', '2026-01-12 10:20:03'),
(10, 'Menu Vegan', 'Menu entièrement végétal, créatif et gourmand. Sans produits d\'origine animale, avec des protéines végétales de qualité.', 20.00, 4, 10, 6, 3, 'menu_vegan.jpg', '2026-01-12 10:20:03'),
(14, 'DZDZD', 'EDZZDZ', 33.03, 0, 0, 2, 3, '69733453342b8.png', '2026-01-22 10:51:18'),
(15, 'bbb', 'bb', 44.00, 0, 0, 1, 4, '697334458a04f.jpg', '2026-01-22 10:58:52'),
(16, 'dscds', 'dsds', 11.00, 0, 0, 5, 4, '69720ac96ccac.webp', '2026-01-22 11:32:25'),
(17, '22', '22', 22.00, 0, 0, 6, 4, '6973349e6131a.jpg', '2026-01-23 08:43:10'),
(18, 'Menu Enfant', '', 0.01, 0, 0, 1, 1, '', '2026-01-30 10:56:10'),
(19, 'esqdqsdqd', '', 0.03, 0, 0, 4, 4, '', '2026-01-30 11:12:49');

-- --------------------------------------------------------

--
-- Structure de la table `plats`
--

CREATE TABLE `plats` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `categorie` enum('entree','plat','dessert') NOT NULL,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plats`
--

INSERT INTO `plats` (`id`, `nom`, `description`, `categorie`, `cree_le`) VALUES
(1, 'Salade César', 'Salade romaine, croûtons, parmesan, sauce César maison', 'entree', '2026-01-12 10:23:22'),
(2, 'Velouté de légumes', 'Velouté crémeux de légumes de saison', 'entree', '2026-01-12 10:23:22'),
(3, 'Terrine de campagne', 'Terrine artisanale servie avec cornichons et pain grillé', 'entree', '2026-01-12 10:23:22'),
(4, 'Salade de chèvre chaud', 'Mesclun, toasts de chèvre chaud, noix et miel', 'entree', '2026-01-12 10:23:22'),
(5, 'Carpaccio de bœuf', 'Fines tranches de bœuf, copeaux de parmesan, roquette', 'entree', '2026-01-12 10:23:22'),
(6, 'Assiette de crudités', 'Assortiment de légumes frais de saison', 'entree', '2026-01-12 10:23:22'),
(7, 'Tartare de saumon', 'Saumon frais mariné, avocat, citron vert', 'entree', '2026-01-12 10:23:22'),
(8, 'Soupe à l\'oignon', 'Soupe gratinée traditionnelle au fromage', 'entree', '2026-01-12 10:23:22'),
(9, 'Steak frites', 'Pièce de bœuf grillée, frites maison', 'plat', '2026-01-12 10:23:22'),
(10, 'Poulet rôti', 'Poulet fermier rôti aux herbes, légumes de saison', 'plat', '2026-01-12 10:23:22'),
(11, 'Saumon grillé', 'Pavé de saumon, sauce citron, riz basmati', 'plat', '2026-01-12 10:23:22'),
(12, 'Blanquette de veau', 'Veau mijoté, sauce onctueuse, riz pilaf', 'plat', '2026-01-12 10:23:22'),
(13, 'Magret de canard', 'Magret de canard poêlé, sauce aux fruits rouges', 'plat', '2026-01-12 10:23:22'),
(14, 'Lasagnes maison', 'Lasagnes bolognaise, béchamel maison', 'plat', '2026-01-12 10:23:22'),
(15, 'Couscous royal', 'Semoule fine, légumes, viandes variées', 'plat', '2026-01-12 10:23:22'),
(16, 'Pavé de cabillaud', 'Poisson blanc grillé, purée de pommes de terre', 'plat', '2026-01-12 10:23:22'),
(17, 'Curry de légumes', 'Légumes variés au curry, lait de coco, riz', 'plat', '2026-01-12 10:23:22'),
(18, 'Risotto aux champignons', 'Risotto crémeux, champignons de saison', 'plat', '2026-01-12 10:23:22'),
(19, 'Burger maison', 'Pain brioche, steak haché, cheddar, frites', 'plat', '2026-01-12 10:23:22'),
(20, 'Gigot d\'agneau', 'Gigot d\'agneau rôti, gratin dauphinois', 'plat', '2026-01-12 10:23:22'),
(21, 'Tarte aux pommes', 'Tarte fine aux pommes caramélisées', 'dessert', '2026-01-12 10:23:22'),
(22, 'Mousse au chocolat', 'Mousse onctueuse au chocolat noir', 'dessert', '2026-01-12 10:23:22'),
(23, 'Crème brûlée', 'Crème vanillée, caramel croustillant', 'dessert', '2026-01-12 10:23:22'),
(24, 'Tarte au citron meringuée', 'Tarte au citron, meringue italienne', 'dessert', '2026-01-12 10:23:22'),
(25, 'Tiramisu', 'Dessert italien aux biscuits et café', 'dessert', '2026-01-12 10:23:22'),
(26, 'Fondant au chocolat', 'Moelleux au chocolat, cœur coulant', 'dessert', '2026-01-12 10:23:22'),
(27, 'Profiteroles', 'Choux garnis de glace vanille, sauce chocolat', 'dessert', '2026-01-12 10:23:22'),
(28, 'Salade de fruits', 'Fruits frais de saison', 'dessert', '2026-01-12 10:23:22'),
(29, 'Île flottante', 'Œufs à la neige, crème anglaise', 'dessert', '2026-01-12 10:23:22'),
(30, 'Tarte tatin', 'Tarte renversée aux pommes caramélisées', 'dessert', '2026-01-12 10:23:22'),
(31, 'test', 'fssffdsfsfsf', 'entree', '2026-01-28 23:53:35');

-- --------------------------------------------------------

--
-- Structure de la table `provoquer`
--

CREATE TABLE `provoquer` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `provoquer`
--

INSERT INTO `provoquer` (`plat_id`, `allergene_id`) VALUES
(1, 1),
(1, 2),
(3, 1),
(4, 1),
(4, 3),
(4, 5),
(7, 6),
(8, 1),
(8, 3),
(11, 6),
(14, 1),
(14, 2),
(14, 3),
(16, 6),
(18, 3),
(19, 1),
(19, 3),
(21, 1),
(21, 2),
(21, 3),
(22, 2),
(22, 3),
(23, 2),
(23, 3),
(24, 1),
(24, 2),
(24, 3),
(25, 1),
(25, 2),
(25, 3),
(26, 1),
(26, 2),
(26, 3),
(27, 1),
(27, 2),
(27, 3),
(30, 1),
(30, 2),
(30, 3);

-- --------------------------------------------------------

--
-- Structure de la table `regimes`
--

CREATE TABLE `regimes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `regimes`
--

INSERT INTO `regimes` (`id`, `nom`) VALUES
(1, 'Classique'),
(4, 'Sans gluten'),
(3, 'Vegan'),
(2, 'Végétarien');

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `themes`
--

INSERT INTO `themes` (`id`, `nom`) VALUES
(1, 'Classique'),
(4, 'Été'),
(5, 'Gastronomique'),
(2, 'Noël'),
(3, 'Pâques'),
(6, 'Végétarien');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `gsm` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `role` enum('utilisateur','employe','admin') DEFAULT 'utilisateur',
  `actif` tinyint(1) DEFAULT 1,
  `cree_le` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `gsm`, `adresse`, `ville`, `code_postal`, `role`, `actif`, `cree_le`, `reset_token`, `reset_token_expiration`) VALUES
(1, 'Admin', 'Vite&Gourmand', 'admin@vite-gourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0556123456', '1 Rue du Traiteur', 'Bordeaux', '33000', 'admin', 1, '2026-01-12 10:13:33', NULL, NULL),
(2, 'Julie', 'Dupont', 'julie@vite-gourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612345678', '10 Avenue de la Cuisine', 'Bordeaux', '33000', 'employe', 1, '2026-01-12 10:13:33', NULL, NULL),
(3, 'José', 'Martinez', 'jose@vite-gourmand.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0623456789', '15 Rue des Livraisons', 'Bordeaux', '33100', 'employe', 1, '2026-01-12 10:13:33', NULL, NULL),
(4, 'Dupont', 'Jean', 'jean.dupont@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0634567890', '123 Rue Exemple', 'Bordeaux', '33000', 'utilisateur', 1, '2026-01-12 10:13:33', NULL, NULL),
(5, 'Martin', 'Marie', 'marie.martin@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0645678901', '45 Avenue Test', 'Bordeaux', '33200', 'utilisateur', 1, '2026-01-12 10:13:33', NULL, NULL),
(6, 'Dubois', 'Pierre', 'pierre.dubois@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0656789012', '78 Boulevard Exemple', 'Talence', '33400', 'utilisateur', 1, '2026-01-12 10:13:33', NULL, NULL),
(7, 'Laurent', 'Sophie', 'sophie.laurent@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0667890123', '12 Place du Marché', 'Mérignac', '33700', 'utilisateur', 1, '2026-01-12 10:13:33', NULL, NULL),
(8, 'robichon', 'quorentin', 'Q@gmail.ocm', '$2y$10$HIJ3BJTRneofWpJCUxUNkeUlQtwbr45EVZDRF2Dvu7O.aQVmF4wCK', '', '', '', '', '', 1, '2026-01-17 17:40:24', NULL, NULL),
(9, 'robichon', 'quorentin', 'quorerentinrobichon2@gmail.com', '$2y$10$.jn9p7ZQDBt9T02ZCLO1TetG71n7wKLUzCTRSAh4/mkW7JbM53XrS', '', '', '', '', '', 1, '2026-01-17 17:43:30', NULL, NULL),
(10, 'T', 'R', 'TR@GMAIL.COM', '$2y$10$O5KGiz3eW7eUwgwHJnKp8eeuF0A9Pl7WbcQSmtlwaKX117OiEJwjy', '', '', '', '', '', 1, '2026-01-20 11:17:50', NULL, NULL),
(11, 'test', 'ttt', 'test@example.com', '$2y$10$VgT6H1PhfRMzT3HYAWQQuO0XGjxmFeXTfmDt5D60Tad5G7aptR13e', '', '', '', '', '', 1, '2026-01-20 11:18:33', NULL, NULL),
(12, 'test', 'test', 'test@test.com', '$2y$10$HEz5dW45hfti63/Ly5fSkeiV/jsztdXqFvHlaycuo/Yi4iIbSZjb6', '', '', '', '', 'utilisateur', 1, '2026-01-21 08:57:11', NULL, NULL),
(13, 'admin', 'admin', 'admin@gmail.com', '$2y$10$x/67NIoAs1CzmaAIGajIR.POfyO7yO/8AtvG9YCVaEoJHkOnf9bqa', '', '', '', '', 'admin', 1, '2026-01-21 09:50:53', NULL, NULL),
(14, '', '', 'bb@gmail.com', '$2y$10$NwtZAGe6npT502KEeh6CMesY1Ryv6oYxmYCiv/mtGWgNt7rd/eD7S', '', '', '', '', 'employe', 1, '2026-01-23 08:56:14', 'a11ef5368e536c27eefd3d1a65fe1aa9d9dd718979b7740ea65678017ee51122', '2026-01-29 01:31:40'),
(15, 'robichon', 'ROB', 'rob@gmail.com', '$2y$10$2N67yzEbetPRwjjE8vmiwu2zhF6MvbeRrR8SBDH9qLp3kjVMteDfG', '', '', '', '', '', 1, '2026-01-27 16:00:18', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `allergenes`
--
ALTER TABLE `allergenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `commande_id` (`commande_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Index pour la table `composer`
--
ALTER TABLE `composer`
  ADD PRIMARY KEY (`menu_id`,`plat_id`),
  ADD KEY `plat_id` (`plat_id`);

--
-- Index pour la table `horaires`
--
ALTER TABLE `horaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_id` (`theme_id`),
  ADD KEY `regime_id` (`regime_id`);

--
-- Index pour la table `plats`
--
ALTER TABLE `plats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `provoquer`
--
ALTER TABLE `provoquer`
  ADD PRIMARY KEY (`plat_id`,`allergene_id`),
  ADD KEY `allergene_id` (`allergene_id`);

--
-- Index pour la table `regimes`
--
ALTER TABLE `regimes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `allergenes`
--
ALTER TABLE `allergenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `horaires`
--
ALTER TABLE `horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `plats`
--
ALTER TABLE `plats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `regimes`
--
ALTER TABLE `regimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`);

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`);

--
-- Contraintes pour la table `composer`
--
ALTER TABLE `composer`
  ADD CONSTRAINT `composer_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `composer_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`),
  ADD CONSTRAINT `menus_ibfk_2` FOREIGN KEY (`regime_id`) REFERENCES `regimes` (`id`);

--
-- Contraintes pour la table `provoquer`
--
ALTER TABLE `provoquer`
  ADD CONSTRAINT `provoquer_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `plats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provoquer_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `allergenes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
