-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Creato il: Ott 16, 2019 alle 22:12
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
  `Jolly` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Authentication`
--

INSERT INTO `Authentication` (`FrontOffice`, `Password`, `Permission`, `Counter`, `Jolly`) VALUES
('admin', '$2y$12$w2udRkTefqcgfOqVFIa6zu4AUURgWzhsnu9091doCNn4Firp.VFg2', 'Admin', 0, 0),
('clerk', '$2y$12$hqVeuuk9y1B2sB/zISy6Kuxo1XSBPAjxXbQsQKbKjdytpcmSWjwZa', 'Clerk', 0, 0),
('test', '$2y$12$zRXRfWvG3pdlrZukzYRGm.bxhxzrmP/DwneGPIGw7Bdgv6x0NnXtW', 'Clerk', 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `Queue`
--

CREATE TABLE `Queue` (
  `ServiceID` int(11) NOT NULL,
  `TicketNumber` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Queue`
--

INSERT INTO `Queue` (`ServiceID`, `TicketNumber`, `Timestamp`) VALUES
(1, 0, '2019-10-16 19:51:59'),
(2, 1, '2019-10-16 19:51:59'),
(2, 0, '2019-10-16 19:51:59'),
(1, 1, '2019-10-16 19:51:59'),
(1, 2, '2019-10-16 19:51:59');

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
('GetParcel', 1, 0),
('SendParcel', 2, 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Authentication`
--
ALTER TABLE `Authentication`
  ADD PRIMARY KEY (`FrontOffice`);

--
-- Indici per le tabelle `Service`
--
ALTER TABLE `Service`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Service`
--
ALTER TABLE `Service`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
