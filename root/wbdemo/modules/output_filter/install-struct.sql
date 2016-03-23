-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 29. Jan 2016 um 19:55
-- Server-Version: 5.6.24
-- PHP-Version: 7.0.1
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
--
-- Datenbank: `dw283-sp3db1`
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_output_filter`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_output_filter`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_output_filter` (
  `name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `value` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`name`)
){TABLE_ENGINE=MyISAM};

