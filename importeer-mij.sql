-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 11, 2024 at 12:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eindopdracht`
--
CREATE DATABASE IF NOT EXISTS `eindopdracht` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `eindopdracht`;

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `id` int(11) NOT NULL,
  `titel` varchar(100) NOT NULL,
  `foutmelding` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `errors`
--

INSERT INTO `errors` (`id`, `titel`, `foutmelding`) VALUES
(1, 'Gebruiker niet gevonden', 'De opgegeven gebruiker bestaat niet. Controleer de gegevens, en probeer het vervolgens opnieuw.'),
(2, 'Wachtwoord onjuist', 'Het opgegeven wachtwoord is incorrect. Vul je gegevens opnieuw in, en probeer het vervolgens opnieuw.'),
(3, 'Verbinding mislukt', 'Het is niet gelukt om verbinding te maken met de database. Probeer het later opnieuw.'),
(4, 'Wachtwoorden komen niet overeen', 'De opgegeven wachtwoorden komen niet overeen. Controleer de gegevens en probeer het opnieuw.'),
(5, 'Gebruiker bestaat al', 'De gebruikersnaam die je probeerde te gebruiken bestaat al. Kies een andere gebruikersnaam en probeer het opnieuw.'),
(6, 'Post versturen mislukt', 'Het is niet gelukt om jouw post te versturen. Probeer het later opnieuw.'),
(7, 'Kon de post niet liken.', 'Het is niet gelukt om de post te liken. Probeer het later opnieuw.'),
(8, 'Versturen mislukt', 'Het is niet gelukt om jouw post te versturen. Probeer het later opnieuw.'),
(9, 'Kon status niet aanpassen', 'Het is niet gelukt om jouw status aan te passen. Probeer het later opnieuw.');

-- --------------------------------------------------------

--
-- Table structure for table `gebruikers`
--

CREATE TABLE `gebruikers` (
  `idGebruiker` int(11) NOT NULL,
  `naam` varchar(128) NOT NULL,
  `status` varchar(128) DEFAULT NULL,
  `wachtwoord` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `idPost` int(11) NOT NULL,
  `auteur` int(11) NOT NULL,
  `body` varchar(512) NOT NULL,
  `likes` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `repliesTo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `succes`
--

CREATE TABLE `succes` (
  `id` int(11) NOT NULL,
  `caption` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'check_circle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `succes`
--

INSERT INTO `succes` (`id`, `caption`, `icon`) VALUES
(1, 'Account aangemaakt', 'person_add'),
(2, 'Je bent ingelogd', 'login'),
(3, 'Tweet geplaatst', 'check_circle'),
(4, 'Post geliked', 'thumb_up'),
(5, 'Reactie geplaatst', 'reply'),
(6, 'Je bent uigelogd', 'logout'),
(7, 'Post succesvol verwijderd', 'delete'),
(8, 'Status geüpdatet', 'check_circle');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gebruikers`
--
ALTER TABLE `gebruikers`
  ADD PRIMARY KEY (`idGebruiker`),
  ADD UNIQUE KEY `naam` (`naam`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`idPost`);

--
-- Indexes for table `succes`
--
ALTER TABLE `succes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gebruikers`
--
ALTER TABLE `gebruikers`
  MODIFY `idGebruiker` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `idPost` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `succes`
--
ALTER TABLE `succes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
