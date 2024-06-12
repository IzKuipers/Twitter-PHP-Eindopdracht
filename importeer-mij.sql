-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 11, 2024 at 06:52 PM
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
-- Database: `twitter`
--
CREATE DATABASE IF NOT EXISTS `twitter` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `twitter`;

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE IF NOT EXISTS `errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(100) NOT NULL,
  `foutmelding` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE IF NOT EXISTS `gebruikers` (
  `idGebruiker` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(128) NOT NULL,
  `status` varchar(128) DEFAULT NULL,
  `wachtwoord` varchar(60) NOT NULL,
  PRIMARY KEY (`idGebruiker`),
  UNIQUE KEY `naam` (`naam`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `auteur` int(11) NOT NULL,
  `body` varchar(512) NOT NULL,
  `likes` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `repliesTo` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPost`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `toast`
--

CREATE TABLE IF NOT EXISTS `toast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'check_circle',
  `type` varchar(50) NOT NULL DEFAULT 'succes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toast`
--

INSERT INTO `toast` (`id`, `caption`, `icon`, `type`) VALUES
(1, 'Account aangemaakt', 'person_add', 'succes'),
(2, 'Je bent ingelogd', 'login', 'succes'),
(3, 'Post geplaatst', 'check_circle', 'succes'),
(4, 'Post geliked', 'thumb_up', 'melding'),
(5, 'Reactie geplaatst', 'reply', 'succes'),
(6, 'Je bent uitgelogd', 'logout', 'melding'),
(7, 'Post succesvol verwijderd', 'delete', 'succes'),
(8, 'Status geüpdatet', 'check_circle', 'melding'),
(9, 'Je moet ingelogd zijn', 'warning', 'waarschuwing');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;