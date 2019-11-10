-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  sam. 09 nov. 2019 à 21:20
-- Version du serveur :  10.4.6-MariaDB
-- Version de PHP :  7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet5`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `width` int(255) NOT NULL,
  `height` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `barcode` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `code`, `description`, `weight`, `width`, `height`, `length`, `barcode`) VALUES
(67, 'BODYSTIM_ELECTR', 'Jeu de 4 électrodes pour Body Stim', 50, 0, 0, 0, '5410984753017'),
(68, 'ELECT_STIMPLUS', 'Jeu de 2 électrodes pour Stim Plus', 50, 0, 0, 0, '5410984008506'),
(69, 'AIC_25135', 'Tourne clé facile', 50, 0, 0, 0, '3661474251359'),
(70, 'AIC_25382', 'Oxymètre de pouls', 50, 0, 0, 0, '4015672106123'),
(71, 'PCV8005', 'Thermo-hygromètre PCV8005', 55, 0, 0, 0, '4250225760227'),
(72, 'BES_23400', 'celui là aussi pue grave', 50, 51, 53, 52, '0000000000054'),
(73, 'FILTRE_HAW501E', 'de la merde', 5, 6, 8, 7, '0000000000009'),
(74, 'nouveau', 'test 2', 101, 102, 104, 103, '105'),
(75, 'FAMILY_THERMOME', 'Thermomètre digital familial Family Thermometer', 45, 1, 0, 0, '5410984548002');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
