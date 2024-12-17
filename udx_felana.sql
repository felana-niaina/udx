-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 déc. 2024 à 08:51
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `udx`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(1, 'Non classé'),
(2, 'Technology');

-- --------------------------------------------------------

--
-- Structure de la table `marketplace`
--

DROP TABLE IF EXISTS `marketplace`;
CREATE TABLE IF NOT EXISTS `marketplace` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` int NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `clicks` varchar(100) NOT NULL,
  `ads` tinyint(1) DEFAULT NULL,
  `picture` text NOT NULL,
  `userId` int NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(30) NOT NULL,
  `clicks` int NOT NULL,
  `userId` int NOT NULL,
  `categoryId` int NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `keywords`, `clicks`, `userId`, `categoryId`, `createdDate`) VALUES
(1, 'Bonjour voici un post', 'Bonjour voici un post, ceci est un essai, merci', 'post', 1, 10, 0, '2024-12-14 00:00:00'),
(2, 'Check new post', 'Testing of new post form', '', 0, 8, 2, '2024-12-15 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `sites`
--

DROP TABLE IF EXISTS `sites`;
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `clicks` varchar(30) NOT NULL,
  `ads` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sites`
--

INSERT INTO `sites` (`id`, `title`, `url`, `description`, `keywords`, `clicks`, `ads`) VALUES
(1, 'Test', 'test.fr', 'bonjour ceci est un test', 'test', '', NULL),
(2, 'Site sponsorise', 'http://pubpayante.fr', 'Ce site est sponsorise par la publicite', 'publicite', '1', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profileTitle` varchar(255) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `created_at`, `profileTitle`, `bio`, `url`, `phone`, `location`, `cover_photo`, `profile_photo`) VALUES
(14, '', 'haingo', 'fara.haingonirina@gmail.com', '$2y$10$YQjKIhAQj53Zt0/1/uBNH.R7skgd9ejSCYGUX7dUv7GVve6ToZ9f.', '2024-12-17 08:49:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '', 'haingo', 'fitahianalova@gmail.com', '$2y$10$REFqlYnzgYiJw8kYU4Qz7eP3TzosXdEaGKsVbQM/5p/mO1xr4gz0G', '2024-12-17 08:00:20', NULL, NULL, NULL, NULL, NULL, 'uploads/1734422584_6761303827b68.png', 'uploads/1734422592_67613040162a2.png');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
