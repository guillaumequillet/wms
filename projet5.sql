-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  ven. 29 nov. 2019 à 15:16
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
  `barcode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `incomings`
--

CREATE TABLE `incomings` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `reference` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  `provider` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `area` varchar(5) NOT NULL,
  `aisle` varchar(5) NOT NULL,
  `col` varchar(5) NOT NULL,
  `level` varchar(5) NOT NULL,
  `concatenate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `outgoings`
--

CREATE TABLE `outgoings` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `reference` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `rows`
--

CREATE TABLE `rows` (
  `id` int(11) NOT NULL,
  `movement` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `article` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `stocks`
--

CREATE TABLE `stocks` (
  `id` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `article` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'superadmin', '$2y$10$MPztmE1MbMP9jyNZqSYxQueEeNUMXOa09g0LxyB0u/m/2XCmfOTWC', 'newemail@test.com', 'superadmin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `incomings`
--
ALTER TABLE `incomings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Index pour la table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `concatenate` (`concatenate`);

--
-- Index pour la table `outgoings`
--
ALTER TABLE `outgoings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Index pour la table `rows`
--
ALTER TABLE `rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article` (`article`),
  ADD KEY `location` (`location`);

--
-- Index pour la table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UC_Stock` (`location`,`article`),
  ADD KEY `location` (`location`),
  ADD KEY `article` (`article`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `incomings`
--
ALTER TABLE `incomings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `outgoings`
--
ALTER TABLE `outgoings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rows`
--
ALTER TABLE `rows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `incomings`
--
ALTER TABLE `incomings`
  ADD CONSTRAINT `incomings_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `outgoings`
--
ALTER TABLE `outgoings`
  ADD CONSTRAINT `outgoings_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `rows`
--
ALTER TABLE `rows`
  ADD CONSTRAINT `rows_ibfk_1` FOREIGN KEY (`article`) REFERENCES `articles` (`id`),
  ADD CONSTRAINT `rows_ibfk_2` FOREIGN KEY (`location`) REFERENCES `locations` (`id`);

--
-- Contraintes pour la table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_ibfk_1` FOREIGN KEY (`location`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `stocks_ibfk_2` FOREIGN KEY (`article`) REFERENCES `articles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
