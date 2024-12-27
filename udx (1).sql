-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 27, 2024 at 06:46 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `udx`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
CREATE TABLE IF NOT EXISTS `ads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adsTypeId` int NOT NULL,
  `contentId` int NOT NULL,
  `budget` int NOT NULL,
  `userId` int NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `adsTypeId`, `contentId`, `budget`, `userId`, `createdDate`) VALUES
(4, 2, 9, 50, 9, '2024-12-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `adstype`
--

DROP TABLE IF EXISTS `adstype`;
CREATE TABLE IF NOT EXISTS `adstype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `contentTable` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `adstype`
--

INSERT INTO `adstype` (`id`, `title`, `contentTable`) VALUES
(1, 'Publicité Search', 'posts'),
(2, 'Publicité Marketplace', 'marketplace');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

DROP TABLE IF EXISTS `billing`;
CREATE TABLE IF NOT EXISTS `billing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cardHolder` varchar(100) NOT NULL,
  `cardNumber` varchar(16) NOT NULL,
  `expirationDate` date NOT NULL,
  `cryptoVisuel` varchar(3) NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cardNumber` (`cardNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `cardHolder`, `cardNumber`, `expirationDate`, `cryptoVisuel`, `createdDate`, `userId`) VALUES
(6, 'Tiana ANDRIAMBOLAMANANA', '5479789798970576', '2025-06-30', 'bvc', '2024-12-16 00:00:00', 9),
(7, 'testset', '23454656575757', '2025-02-28', 'hyu', '2024-12-16 00:00:00', 9),
(8, 'Tiana ANDRIAMBOLAMANANA', '1238746876587468', '2025-06-30', 'AZE', '2024-12-21 00:00:00', 9);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(1, 'Non classé'),
(2, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `postId` int NOT NULL,
  `userId` int NOT NULL,
  `commentText` text NOT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `postId` (`postId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `postId`, `userId`, `commentText`, `createdAt`) VALUES
(8, 1, 10, 'erererere', '2024-12-27 10:55:11'),
(9, 1, 10, 'yyyyyyy', '2024-12-27 12:31:12');

-- --------------------------------------------------------

--
-- Table structure for table `marketplace`
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
  `isFeatured` int NOT NULL DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `marketplace`
--

INSERT INTO `marketplace` (`id`, `title`, `description`, `price`, `keywords`, `clicks`, `ads`, `picture`, `userId`, `isFeatured`, `createdDate`) VALUES
(1, 'iPhone 13', 'Bonjour je vends mon iphone 13 pas cher', 0, 'iphone', '', NULL, '', 0, 0, '2024-12-14 00:00:00'),
(6, 'Ordi portable', 'Asus zenbook i7.', 1000, 'asus, ordinateur', '', NULL, 'uploads/1734800329_6766f3c9ef8ef.png', 9, 0, '2024-12-15 00:00:00'),
(7, 'Ecran TV', 'Ecran plat 32\"', 200, 'samsung, écran', '', NULL, '', 8, 0, '2024-12-15 00:00:00'),
(9, 'Iphone 6S', 'Iphone 6S Gold', 150, 'iphone, iphone 16', '', NULL, '', 9, 1, '2024-12-15 00:00:00'),
(10, 'cuisinière à gaz', 'cuisinière 4 feux avec four à gaz', 200, 'four, cuisinière', '', NULL, 'uploads/1734276596_675ef5f420d81.jpg', 8, 0, '2024-12-15 00:00:00'),
(11, 'termosy', 'jskgljg', 10, 'test', '', NULL, 'uploads/1734276929_675ef7415e294.webp', 8, 0, '2024-12-15 00:00:00'),
(12, 'Armoire 3 portes', 'Armoire 3 porte en mélamine', 200, 'armoire, mélamine', '', NULL, 'uploads/1734361124_6760402476864.webp', 9, 0, '2024-12-16 00:00:00'),
(13, 'New product', 'Product description', 10, 'product', '', NULL, 'uploads/1734452314_6761a45aa113f.jpg', 10, 0, '2024-12-17 00:00:00'),
(14, 'Other product', 'Other product des', 23, 'asus, ordinateur', '', NULL, 'uploads/1734452435_6761a4d3e8c8c.jpg', 10, 0, '2024-12-17 00:00:00'),
(15, 'Socolait', 'azerty', 20, 'yaourt', '', NULL, 'uploads/1735280448_676e4740f015c.png', 12, 0, '2024-12-27 09:20:48');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fromUserId` int NOT NULL,
  `toUserId` int NOT NULL,
  `parentId` int DEFAULT NULL,
  `isRead` int NOT NULL DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedDate` datetime DEFAULT NULL,
  `contentId` int DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `subject`, `content`, `fromUserId`, `toUserId`, `parentId`, `isRead`, `createdDate`, `updatedDate`, `contentId`, `budget`) VALUES
(1, 'Test new message', 'This is a test', 9, 10, NULL, 0, '2024-12-21 22:33:40', NULL, NULL, NULL),
(2, 'Test new message', 'Answer of the first message', 10, 9, NULL, 0, '2024-12-21 23:23:36', NULL, NULL, NULL),
(3, 'A new subject', 'He how  are you ?', 11, 10, NULL, 0, '2024-12-21 23:26:51', NULL, NULL, NULL),
(4, 'A new subject', 'I m fine thanks', 10, 7, NULL, 0, '2024-12-21 23:38:42', NULL, NULL, NULL),
(33, 'felana', 'test par felana', 13, 12, 0, 0, '2024-12-27 21:10:17', NULL, NULL, NULL),
(34, 'felana', 'test par felana', 13, 12, 34, 0, '2024-12-27 21:11:54', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
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
  `isFeatured` int NOT NULL DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `keywords`, `clicks`, `userId`, `categoryId`, `isFeatured`, `createdDate`) VALUES
(1, 'Bonjour voici un post', 'Bonjour voici un post, ceci est un essai, merci', 'post', 1, 10, 0, 0, '2024-12-14 00:00:00'),
(2, 'Check new post', 'Testing of new post form', '', 0, 8, 2, 0, '2024-12-15 00:00:00'),
(3, 'My first post', 'This is my first post for test', '', 0, 9, 1, 0, '2024-12-16 00:00:00'),
(4, 'New post for Ilies', 'desc ilies post', '', 0, 13, 2, 0, '2024-12-17 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sites`
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
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`id`, `title`, `url`, `description`, `keywords`, `clicks`, `ads`) VALUES
(1, 'Test', 'test.fr', 'bonjour ceci est un test', 'test', '', NULL),
(2, 'Site sponsorise', 'http://pubpayante.fr', 'Ce site est sponsorise par la publicite', 'publicite', '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `created_at`, `profileTitle`, `bio`, `url`, `phone`, `location`, `cover_photo`, `profile_photo`) VALUES
(7, '', 'Tina', 'dev.tiana261@gmail.comm', '$2y$10$0DopVvY.kDT6yLX/47ju6e2udcPbkpE4e2njGId.MMEUhyckgM17W', '2024-12-15 05:13:44', NULL, NULL, NULL, NULL, NULL, NULL, ''),
(8, 'Tiana Nandrasana', 'herrima', 'dev.tiana261@gmail.com2', '$2y$10$R9hHn4oMKLixlqfvKF7rVONe8vdT12swtmOaCtnxRxjJlUb.JKyrK', '2024-12-15 05:20:50', 'Developpeur', 'Expert en solution digital', '', '', 'Madagascar', 'uploads/1734274953_675eef89d99c9.jpg', ''),
(9, '', 'tiana261', 'dev.tiana261@gmail.coma', '$2y$10$CT8mI3fWA5dXH8p0biNfQe88OEVxbsDbac4wFQ/bVAGPpACHxWGC2', '2024-12-15 15:52:47', '', '', '', '', '', 'uploads/1734361051_67603fdb75105.jpg', 'uploads/1734800299_6766f3ab43e6a.jpg'),
(10, 'Ilies Malki', 'ilies', 'ilies@mail.com', '$2y$10$OhGKLsWJquodfo2JxAUoJu.PHahDBteC8zc1o0ZC6bt292WDCU3MK', '2024-12-17 16:15:39', 'Développeur', 'Test', '', '', '', 'uploads/1734452195_6761a3e370159.webp', ''),
(11, '', 'tax', 'dev.tiana261@gmail.com', '$2y$10$/umY3wM3oMon491bh7C73OzaEBjcNpPzTyS.r9fph1.HaR1/KnSAy', '2024-12-17 16:49:29', NULL, NULL, NULL, NULL, NULL, NULL, ''),
(12, '', 'Felana', 'nirina.felananiaina@gmail.com', '$2y$10$UN5iEHgb4JDJiCVZj70XP.NnNERBCmsI2kjL7fwfd89ousqVABx6O', '2024-12-27 05:37:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, '', 'haingo', 'fara.haingonirina@gmail.com', '$2y$10$a6/2IANbmxU.2QkmMIFLCeIXqAWfD7WAYtsuQYMT.RRMHSAiQsSfS', '2024-12-27 07:04:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
