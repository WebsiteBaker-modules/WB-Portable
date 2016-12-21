-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Aug 2014 um 10:46
-- Server Version: 5.5.32
-- PHP-Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
-- --------------------------------------------------------
-- Database structure for module 'news'
--
-- Replacements: {TABLE_PREFIX}, {TABLE_ENGINE}, {FIELD_COLLATION}
--
-- --------------------------------------------------------
--
-- Table structure addons`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}addons`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}addons` (
  `addon_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `directory` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `description` text{FIELD_COLLATION} NOT NULL,
  `function` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `version` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `platform` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `author` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `license` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  PRIMARY KEY (`addon_id`)
){TABLE_ENGINE};
ALTER TABLE `{TABLE_PREFIX}addons` ADD UNIQUE `ident` ( `directory` );
-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

