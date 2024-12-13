-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 12 déc. 2024 à 05:14
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
-- Base de données : `udx`
--

-- --------------------------------------------------------

--
-- Structure de la table `marketplace`
--

CREATE TABLE `marketplace` (
  `id` int(40) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` int(10) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `clicks` varchar(100) NOT NULL,
  `ads` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `marketplace`
--

INSERT INTO `marketplace` (`id`, `title`, `description`, `price`, `keywords`, `clicks`, `ads`) VALUES
(1, 'iPhone 13', 'Bonjour je vends mon iphone 13 pas cher', 0, 'iphone', '', NULL),
(2, 'iPhone 16', 'iPhone 16 a vendre sous emballage', 600, 'iphone', '', NULL),
(3, 'Tiana dev web', 'Tiana dev web freelance', 100, 'freelance, tiana', '', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(30) NOT NULL,
  `clicks` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `keywords`, `clicks`) VALUES
(1, 'Bonjour voici un post', 'Bonjour voici un post, ceci est un essai, merci', 'post', 1);

-- --------------------------------------------------------

--
-- Structure de la table `sites`
--

CREATE TABLE `sites` (
  `id` int(1) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(30) NOT NULL,
  `description` varchar(300) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `clicks` varchar(30) NOT NULL,
  `ads` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sites`
--

INSERT INTO `sites` (`id`, `title`, `url`, `description`, `keywords`, `clicks`, `ads`) VALUES
(1, 'Test', 'test.fr', 'bonjour ceci est un test', 'test', '', NULL),
(2, 'Site sponsorise', 'http://pubpayante.fr', 'Ce site est sponsorise par la publicite', 'publicite', '1', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
