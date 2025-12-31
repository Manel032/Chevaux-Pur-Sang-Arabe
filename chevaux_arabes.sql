-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 31 déc. 2025 à 02:26
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
-- Base de données : `chevaux_arabes`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `api_key` varchar(64) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cheval_images`
--

CREATE TABLE `cheval_images` (
  `id` int(11) NOT NULL,
  `cheval_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `chevaux`
--

CREATE TABLE `chevaux` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `ddn` date DEFAULT NULL,
  `mort` date DEFAULT NULL,
  `pays_origine` varchar(100) DEFAULT NULL,
  `pays_vie` varchar(100) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `jockey_id` int(11) DEFAULT NULL,
  `pere_id` int(11) DEFAULT NULL,
  `mere_id` int(11) DEFAULT NULL,
  `blockchain_hash` varchar(64) DEFAULT NULL,
  `blockchain_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `date` date DEFAULT NULL,
  `cheval_id` int(11) DEFAULT NULL,
  `trophee` varchar(255) DEFAULT NULL,
  `gains` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jockeys`
--

CREATE TABLE `jockeys` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `nationalite` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `races`
--

CREATE TABLE `races` (
  `id` int(11) NOT NULL,
  `cheval_id` int(11) DEFAULT NULL,
  `nom_course` varchar(255) DEFAULT NULL,
  `date_course` date DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `gain` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cheval_images`
--
ALTER TABLE `cheval_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cheval_id` (`cheval_id`);

--
-- Index pour la table `chevaux`
--
ALTER TABLE `chevaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner` (`owner_id`),
  ADD KEY `fk_jockey` (`jockey_id`),
  ADD KEY `pere_id` (`pere_id`),
  ADD KEY `mere_id` (`mere_id`);

--
-- Index pour la table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cheval` (`cheval_id`);

--
-- Index pour la table `jockeys`
--
ALTER TABLE `jockeys`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `races`
--
ALTER TABLE `races`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cheval_id` (`cheval_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cheval_images`
--
ALTER TABLE `cheval_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `chevaux`
--
ALTER TABLE `chevaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jockeys`
--
ALTER TABLE `jockeys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `races`
--
ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cheval_images`
--
ALTER TABLE `cheval_images`
  ADD CONSTRAINT `cheval_images_ibfk_1` FOREIGN KEY (`cheval_id`) REFERENCES `chevaux` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `chevaux`
--
ALTER TABLE `chevaux`
  ADD CONSTRAINT `chevaux_ibfk_1` FOREIGN KEY (`pere_id`) REFERENCES `chevaux` (`id`),
  ADD CONSTRAINT `chevaux_ibfk_2` FOREIGN KEY (`mere_id`) REFERENCES `chevaux` (`id`),
  ADD CONSTRAINT `fk_jockey` FOREIGN KEY (`jockey_id`) REFERENCES `jockeys` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_owner` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_cheval` FOREIGN KEY (`cheval_id`) REFERENCES `chevaux` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `races`
--
ALTER TABLE `races`
  ADD CONSTRAINT `races_ibfk_1` FOREIGN KEY (`cheval_id`) REFERENCES `chevaux` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
