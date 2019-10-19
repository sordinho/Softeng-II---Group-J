-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Ott 17, 2019 alle 18:20
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

-- --------------------------------------------------------

--
-- Struttura della tabella `Authentication`
--

CREATE TABLE `Authentication` (
  `FrontOffice` varchar(55) NOT NULL,
  `Password` varchar(65) NOT NULL,
  `Permission` varchar(8) NOT NULL DEFAULT 'Clerk',
  `Counter` int(11) NOT NULL,
  `ServiceID` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Authentication`
--

INSERT INTO `Authentication` (`FrontOffice`, `Password`, `Permission`, `Counter`, `ServiceID`) VALUES
('admin', '$2y$12$w2udRkTefqcgfOqVFIa6zu4AUURgWzhsnu9091doCNn4Firp.VFg2', 'Admin', 0, -1),
('clerk1', '$2y$12$p7lClUyaBRS8RzbVJw7uEOjx7mhK8J4B1YK8Csrf4wSoZHZYnLYWu', 'Clerk', 0, 0),
('frontoffice1', '$2y$12$ZOB4hLXsBQmRWwU7u0hP4e3GUbyOEg7Gll1ZJMEDd4d4sWiqDE8by', 'Clerk', 0, 1),
('frontoffice2', '$2y$12$EtweUxc9g9djZ8rCf9u9l.wgackyYl9iHKqiXSGQRiQR./rdnR.HK', 'Clerk', 0, 2),
('frontofficeMultipleService', '$2y$12$x9bvOXvWej6YJ6lQMnqw6.aDp/hjYx94z.eQu6XUM6bJIyYKymkd2', 'Clerk', 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `Queue`
--

CREATE TABLE `Queue` (
  `ID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `TicketNumber` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Queue`
--

INSERT INTO `Queue` (`ID`, `ServiceID`, `TicketNumber`, `Timestamp`) VALUES
(1, 1, 0, '2019-10-16 19:51:59'),
(2, 2, 1, '2019-10-16 19:51:59'),
(3, 2, 0, '2019-10-16 19:51:59'),
(4, 1, 1, '2019-10-16 19:51:59'),
(5, 1, 2, '2019-10-16 19:51:59'),
(6, 1, 3, '2019-10-17 15:48:15'),
(7, 1, 4, '2019-10-17 16:10:09'),
(8, 1, 5, '2019-10-17 16:10:53');

-- --------------------------------------------------------

--
-- Struttura della tabella `Service`
--

CREATE TABLE `Service` (
  `Name` varchar(65) NOT NULL,
  `ID` int(11) NOT NULL,
  `Counter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Service`
--

INSERT INTO `Service` (`Name`, `ID`, `Counter`) VALUES
('Packages', 1, 0),
('Accounts', 2, 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Authentication`
--
ALTER TABLE `Authentication`
  ADD PRIMARY KEY (`FrontOffice`);

--
-- Indici per le tabelle `Queue`
--
ALTER TABLE `Queue`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `Service`
--
ALTER TABLE `Service`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`);
--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Queue`
--
ALTER TABLE `Queue`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT per la tabella `Service`
--
ALTER TABLE `Service`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
