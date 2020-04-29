-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 10.35.47.221:3306
-- Erstellungszeit: 28. Apr 2020 um 17:26
-- Server-Version: 5.7.28
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `k95449_planningpoker`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Runde`
--

CREATE TABLE `Runde` (
  `ID` int(11) NOT NULL,
  `Spiel` int(11) NOT NULL,
  `Abgeschlossen` int(1) NOT NULL DEFAULT '0',
  `Datum` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `Runde`
--

INSERT INTO `Runde` (`ID`, `Spiel`, `Abgeschlossen`, `Datum`) VALUES
(31, 13, 1, '2020-04-28 16:38:20'),
(32, 13, 1, '2020-04-28 16:39:07'),
(33, 13, 1, '2020-04-28 16:39:36'),
(34, 12, 1, '2020-04-28 16:40:28'),
(35, 13, 0, '2020-04-28 16:42:09'),
(36, 12, 0, '2020-04-28 16:42:14');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Spiel`
--

CREATE TABLE `Spiel` (
  `ID` int(11) NOT NULL,
  `Task` varchar(128) NOT NULL,
  `Beschreibung` varchar(256) NOT NULL,
  `Kartenset` json NOT NULL,
  `Datum` datetime NOT NULL,
  `Adminuser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `Spiel`
--

INSERT INTO `Spiel` (`ID`, `Task`, `Beschreibung`, `Kartenset`, `Datum`, `Adminuser`) VALUES
(12, 'Kunde registrieren', 'Als Kunde will ich andere Kunden registrieren lassen', '[\"0\", \"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\", \"☕\"]', '2020-04-28 16:37:05', 41),
(13, 'Projekte beitreten', 'Als Kunde will ich Projekten beitreten können', '[\"0\", \"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\", \"☕\"]', '2020-04-28 16:37:38', 40);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
  `ID` int(11) NOT NULL,
  `Vorname` varchar(20) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Passwort` varchar(255) NOT NULL,
  `Datum` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `User`
--

INSERT INTO `User` (`ID`, `Vorname`, `Mail`, `Passwort`, `Datum`) VALUES
(40, 'Beispiel1', 'beispiel@example.com', '$2y$10$fcEW3TrzTjoCzceXL6NDCOKoC5OE8btHPOjLf91keKg9jFQhDmadW', '2020-04-28 16:36:03'),
(41, 'Beispiel2', 'beispiel2@example.com', '$2y$10$S.a0XLgUEFwuG5PDH1GWquAzXICm2WE7rAoLgMa7ortPdtdGSBHu6', '2020-04-28 16:38:06');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `UserRunde`
--

CREATE TABLE `UserRunde` (
  `Runde` int(11) NOT NULL,
  `User` int(11) NOT NULL,
  `Karte` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `UserRunde`
--

INSERT INTO `UserRunde` (`Runde`, `User`, `Karte`) VALUES
(31, 40, '7'),
(31, 41, '5'),
(32, 40, '6'),
(32, 41, '8'),
(33, 40, '☕'),
(33, 41, '☕'),
(34, 40, '60'),
(34, 41, '80'),
(35, 40, ''),
(35, 41, ''),
(36, 40, ''),
(36, 41, '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Runde`
--
ALTER TABLE `Runde`
  ADD PRIMARY KEY (`ID`,`Spiel`),
  ADD KEY `Spiel` (`Spiel`);

--
-- Indizes für die Tabelle `Spiel`
--
ALTER TABLE `Spiel`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Adminuser` (`Adminuser`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Vorname` (`Vorname`),
  ADD UNIQUE KEY `Mail` (`Mail`);

--
-- Indizes für die Tabelle `UserRunde`
--
ALTER TABLE `UserRunde`
  ADD PRIMARY KEY (`Runde`,`User`),
  ADD KEY `User` (`User`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Runde`
--
ALTER TABLE `Runde`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT für Tabelle `Spiel`
--
ALTER TABLE `Spiel`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Runde`
--
ALTER TABLE `Runde`
  ADD CONSTRAINT `Runde_ibfk_1` FOREIGN KEY (`Spiel`) REFERENCES `Spiel` (`ID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `Spiel`
--
ALTER TABLE `Spiel`
  ADD CONSTRAINT `Spiel_ibfk_1` FOREIGN KEY (`Adminuser`) REFERENCES `User` (`ID`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `UserRunde`
--
ALTER TABLE `UserRunde`
  ADD CONSTRAINT `UserRunde_ibfk_1` FOREIGN KEY (`Runde`) REFERENCES `Runde` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `UserRunde_ibfk_2` FOREIGN KEY (`User`) REFERENCES `User` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
