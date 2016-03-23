-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 01. Feb 2016 um 19:54
-- Server-Version: 5.6.24
-- PHP-Version: 7.0.1
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `mod_droplets`
-- Replacements: {TABLE_PREFIX}, {TABLE_ENGINE}, {FIELD_COLLATION}
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_droplets`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_droplets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32){FIELD_COLLATION} NOT NULL DEFAULT '',
  `code` longtext{FIELD_COLLATION} NOT NULL,
  `description` text{FIELD_COLLATION} NOT NULL,
  `modified_when` int(11) NOT NULL DEFAULT '0',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `admin_edit` int(11) NOT NULL DEFAULT '0',
  `admin_view` int(11) NOT NULL DEFAULT '0',
  `show_wysiwyg` int(11) NOT NULL DEFAULT '0',
  `comments` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`id`)
){TABLE_ENGINE=MyISAM};

