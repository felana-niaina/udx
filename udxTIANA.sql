-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 21 déc. 2024 à 21:48
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
-- Structure de la table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `adsTypeId` int(11) NOT NULL,
  `contentId` int(11) NOT NULL,
  `budget` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `createdDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `ads`
--

INSERT INTO `ads` (`id`, `adsTypeId`, `contentId`, `budget`, `userId`, `createdDate`) VALUES
(4, 2, 9, 50, 9, '2024-12-21');

-- --------------------------------------------------------

--
-- Structure de la table `adstype`
--

CREATE TABLE `adstype` (
  `id` int(11) NOT NULL,
  `title` varchar(40) NOT NULL,
  `contentTable` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `adstype`
--

INSERT INTO `adstype` (`id`, `title`, `contentTable`) VALUES
(1, 'Publicité Search', 'posts'),
(2, 'Publicité Marketplace', 'marketplace');

-- --------------------------------------------------------

--
-- Structure de la table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `cardHolder` varchar(100) NOT NULL,
  `cardNumber` varchar(16) NOT NULL,
  `expirationDate` date NOT NULL,
  `cryptoVisuel` varchar(3) NOT NULL,
  `createdDate` date NOT NULL DEFAULT current_timestamp(),
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `billing`
--

INSERT INTO `billing` (`id`, `cardHolder`, `cardNumber`, `expirationDate`, `cryptoVisuel`, `createdDate`, `userId`) VALUES
(6, 'Tiana ANDRIAMBOLAMANANA', '5479789798970576', '2025-06-30', 'bvc', '2024-12-16', 9),
(7, 'testset', '23454656575757', '2025-02-28', 'hyu', '2024-12-16', 9),
(8, 'Tiana ANDRIAMBOLAMANANA', '1238746876587468', '2025-06-30', 'AZE', '2024-12-21', 9);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(2) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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

CREATE TABLE `marketplace` (
  `id` int(40) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` int(10) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `clicks` varchar(100) NOT NULL,
  `ads` tinyint(1) DEFAULT NULL,
  `picture` text NOT NULL,
  `userId` int(11) NOT NULL,
  `isFeatured` int(1) NOT NULL DEFAULT 0,
  `createdDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `marketplace`
--

INSERT INTO `marketplace` (`id`, `title`, `description`, `price`, `keywords`, `clicks`, `ads`, `picture`, `userId`, `isFeatured`, `createdDate`) VALUES
(1, 'iPhone 13', 'Bonjour je vends mon iphone 13 pas cher', 0, 'iphone', '', NULL, '', 0, 0, '2024-12-14'),
(6, 'Ordi portable', 'Asus zenbook i7.', 1000, 'asus, ordinateur', '', NULL, 'uploads/1734800329_6766f3c9ef8ef.png', 9, 0, '2024-12-15'),
(7, 'Ecran TV', 'Ecran plat 32\"', 200, 'samsung, écran', '', NULL, '', 8, 0, '2024-12-15'),
(9, 'Iphone 6S', 'Iphone 6S Gold', 150, 'iphone, iphone 16', '', NULL, '', 9, 1, '2024-12-15'),
(10, 'cuisinière à gaz', 'cuisinière 4 feux avec four à gaz', 200, 'four, cuisinière', '', NULL, 'uploads/1734276596_675ef5f420d81.jpg', 8, 0, '2024-12-15'),
(11, 'termosy', 'jskgljg', 10, 'test', '', NULL, 'uploads/1734276929_675ef7415e294.webp', 8, 0, '2024-12-15'),
(12, 'Armoire 3 portes', 'Armoire 3 porte en mélamine', 200, 'armoire, mélamine', '', NULL, 'uploads/1734361124_6760402476864.webp', 9, 0, '2024-12-16'),
(13, 'New product', 'Product description', 10, 'product', '', NULL, 'uploads/1734452314_6761a45aa113f.jpg', 10, 0, '2024-12-17'),
(14, 'Other product', 'Other product des', 23, 'asus, ordinateur', '', NULL, 'uploads/1734452435_6761a4d3e8c8c.jpg', 10, 0, '2024-12-17');

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fromUserId` int(11) NOT NULL,
  `toUserId` int(11) NOT NULL,
  `isRead` int(1) NOT NULL DEFAULT 0,
  `createdDate` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `subject`, `content`, `fromUserId`, `toUserId`, `isRead`, `createdDate`, `updatedDate`) VALUES
(1, 'Test new message', 'This is a test', 9, 10, 0, '2024-12-21 22:33:40', NULL),
(2, 'Test new message', 'Answer of the first message', 10, 9, 0, '2024-12-21 23:23:36', NULL),
(3, 'A new subject', 'He how  are you ?', 11, 10, 0, '2024-12-21 23:26:51', NULL),
(4, 'A new subject', 'I\'m fine thanks', 10, 7, 0, '2024-12-21 23:38:42', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(30) NOT NULL,
  `clicks` int(30) NOT NULL,
  `userId` int(40) NOT NULL,
  `categoryId` int(2) NOT NULL,
  `isFeatured` int(1) NOT NULL DEFAULT 0,
  `createdDate` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `keywords`, `clicks`, `userId`, `categoryId`, `isFeatured`, `createdDate`) VALUES
(1, 'Bonjour voici un post', 'Bonjour voici un post, ceci est un essai, merci', 'post', 1, 10, 0, 0, '2024-12-14'),
(2, 'Check new post', 'Testing of new post form', '', 0, 8, 2, 0, '2024-12-15'),
(3, 'My first post', 'This is my first post for test', '', 0, 9, 1, 0, '2024-12-16'),
(4, 'New post for Ilies', 'desc ilies post', '', 0, 10, 2, 0, '2024-12-17');

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

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `profileTitle` varchar(255) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `created_at`, `profileTitle`, `bio`, `url`, `phone`, `location`, `cover_photo`, `profile_photo`) VALUES
(7, '', 'Tina', 'dev.tiana261@gmail.comm', '$2y$10$0DopVvY.kDT6yLX/47ju6e2udcPbkpE4e2njGId.MMEUhyckgM17W', '2024-12-15 05:13:44', NULL, NULL, NULL, NULL, NULL, NULL, ''),
(8, 'Tiana Nandrasana', 'herrima', 'dev.tiana261@gmail.com2', '$2y$10$R9hHn4oMKLixlqfvKF7rVONe8vdT12swtmOaCtnxRxjJlUb.JKyrK', '2024-12-15 05:20:50', 'Developpeur', 'Expert en solution digital', '', '', 'Madagascar', 'uploads/1734274953_675eef89d99c9.jpg', ''),
(9, '', 'tiana261', 'dev.tiana261@gmail.coma', '$2y$10$CT8mI3fWA5dXH8p0biNfQe88OEVxbsDbac4wFQ/bVAGPpACHxWGC2', '2024-12-15 15:52:47', '', '', '', '', '', 'uploads/1734361051_67603fdb75105.jpg', 'uploads/1734800299_6766f3ab43e6a.jpg'),
(10, 'Ilies Malki', 'ilies', 'ilies@mail.com', '$2y$10$OhGKLsWJquodfo2JxAUoJu.PHahDBteC8zc1o0ZC6bt292WDCU3MK', '2024-12-17 16:15:39', 'Développeur', 'Test', '', '', '', 'uploads/1734452195_6761a3e370159.webp', ''),
(11, '', 'tax', 'dev.tiana261@gmail.com', '$2y$10$/umY3wM3oMon491bh7C73OzaEBjcNpPzTyS.r9fph1.HaR1/KnSAy', '2024-12-17 16:49:29', NULL, NULL, NULL, NULL, NULL, NULL, '');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `adstype`
--
ALTER TABLE `adstype`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cardNumber` (`cardNumber`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `marketplace`
--
ALTER TABLE `marketplace`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT pour la table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `adstype`
--
ALTER TABLE `adstype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `marketplace`
--
ALTER TABLE `marketplace`
  MODIFY `id` int(40) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
