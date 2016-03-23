-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 01. Feb 2016 um 21:09
-- Server-Version: 5.6.24
-- PHP-Version: 7.0.1
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
--
-- Datenbank: `dw283-sp3db1`
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_wysiwyg`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_wysiwyg`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_wysiwyg` (
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `content` longtext{FIELD_COLLATION} NOT NULL,
  `text` longtext{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`section_id`)
){TABLE_ENGINE=MyISAM};

