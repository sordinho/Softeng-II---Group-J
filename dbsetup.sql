-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Ott 16, 2019 alle 19:49
-- Versione del server: 5.7.27-0ubuntu0.16.04.1
-- Versione PHP: 7.0.33-0ubuntu0.16.04.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `softeng2`
--
CREATE DATABASE IF NOT EXISTS `softeng2` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `softeng2`;

-- --------------------------------------------------------

--
-- Struttura della tabella `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `Username` varchar(55) NOT NULL,
  `Password` varchar(65) NOT NULL,
  `Permission` varchar(8) NOT NULL DEFAULT 'Clerk',
  PRIMARY KEY (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `User`
--

INSERT INTO `User` (`Username`, `Password`, `Permission`) VALUES
('admin', '$2y$12$w2udRkTefqcgfOqVFIa6zu4AUURgWzhsnu9091doCNn4Firp.VFg2', 'Admin'),
('clerk', '$2y$12$hqVeuuk9y1B2sB/zISy6Kuxo1XSBPAjxXbQsQKbKjdytpcmSWjwZa', 'Clerk'),
('test', '$2y$12$zRXRfWvG3pdlrZukzYRGm.bxhxzrmP/DwneGPIGw7Bdgv6x0NnXtW', 'Clerk');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
