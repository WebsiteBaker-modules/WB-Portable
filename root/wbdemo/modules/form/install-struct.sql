-- --------------------------------------------------------
-- SQL-Import-Struct-File
-- generated with ConvertDump Version 0.2.1
-- WebsiteBaker Edition
-- Creation time: Tue, 03 Feb 2015 11:25:46 +0100
-- --------------------------------------------------------
-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 03. Feb 2015 um 11:14
-- Server Version: 5.5.27
-- PHP-Version: 5.4.19
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_form_fields`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_form_fields`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_form_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `type` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `required` int(11) NOT NULL DEFAULT '0',
  `value` text{FIELD_COLLATION} NOT NULL,
  `extra` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`field_id`)
){TABLE_ENGINE=MyISAM};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_form_settings`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_form_settings`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_form_settings` (
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `header` text{FIELD_COLLATION} NOT NULL,
  `field_loop` text{FIELD_COLLATION} NOT NULL,
  `footer` text{FIELD_COLLATION} NOT NULL,
  `email_to` text{FIELD_COLLATION} NOT NULL,
  `email_from` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `email_fromname` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `email_subject` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `success_page`  int(11) NOT NULL DEFAULT '0',
  `success_email_to` text{FIELD_COLLATION} NOT NULL,
  `success_email_from` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `success_email_fromname` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `success_email_text` text{FIELD_COLLATION} NOT NULL,
  `success_email_subject` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `stored_submissions` int(11) NOT NULL DEFAULT '0',
  `max_submissions` int(11) NOT NULL DEFAULT '0',
  `perpage_submissions` int(11) NOT NULL DEFAULT '10',
  `use_captcha` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`section_id`)
){TABLE_ENGINE=MyISAM};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_form_submissions`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_form_submissions`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_form_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `submitted_when` int(11) NOT NULL DEFAULT '0',
  `submitted_by` int(11) NOT NULL DEFAULT '0',
  `body` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`submission_id`)
){TABLE_ENGINE=MyISAM};
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- --------------------------------------------------------
-- END OF SQL-Import-Struct-File
-- --------------------------------------------------------
