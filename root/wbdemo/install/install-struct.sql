-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Aug 2014 um 10:46
-- Server Version: 5.5.32
-- PHP-Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- --------------------------------------------------------
-- Database structure for module 'news'
--
-- Replacements: {TABLE_PREFIX}, {TABLE_ENGINE}, {FIELD_COLLATION}
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `addons`
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
--
-- Tabellenstruktur für Tabelle `groups`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}groups`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `system_permissions` text{FIELD_COLLATION} NOT NULL,
  `module_permissions` text{FIELD_COLLATION} NOT NULL,
  `template_permissions` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`group_id`)
){TABLE_ENGINE};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `pages`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}pages`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `root_parent` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `link` text{FIELD_COLLATION} NOT NULL,
  `target` varchar(7){FIELD_COLLATION} NOT NULL DEFAULT '',
  `page_title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `menu_title` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `description` text{FIELD_COLLATION} NOT NULL,
  `keywords` text{FIELD_COLLATION} NOT NULL,
  `page_trail` text{FIELD_COLLATION} NOT NULL,
  `template` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `visibility` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `menu` int(11) NOT NULL DEFAULT '0',
  `language` varchar(5){FIELD_COLLATION} NOT NULL DEFAULT '',
  `searching` int(11) NOT NULL DEFAULT '0',
  `admin_groups` text{FIELD_COLLATION} NOT NULL,
  `admin_users` text{FIELD_COLLATION} NOT NULL,
  `viewing_groups` text{FIELD_COLLATION} NOT NULL,
  `viewing_users` text{FIELD_COLLATION} NOT NULL,
  `modified_when` int(11) NOT NULL DEFAULT '0',
  `modified_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`)
){TABLE_ENGINE};
ALTER TABLE `{TABLE_PREFIX}pages` ADD `page_icon` varchar(512){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `page_title`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `menu_icon_0` varchar(512){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `menu_title`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `menu_icon_1` varchar(512){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `menu_icon_0`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `tooltip` varchar(512){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `menu_icon_1`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `custom01` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `modified_by`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `custom02` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `custom01`;
ALTER TABLE `{TABLE_PREFIX}pages` ADD `page_code` int(11) NOT NULL DEFAULT '0' AFTER `custom02`;

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `search`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}search`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}search` (
  `search_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `value` text{FIELD_COLLATION} NOT NULL,
  `extra` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`search_id`)
){TABLE_ENGINE};
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `sections`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}sections`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `module` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `block` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `publ_start` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '0',
  `publ_end` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '0',
  PRIMARY KEY (`section_id`)
){TABLE_ENGINE};
ALTER TABLE `{TABLE_PREFIX}sections` ADD `title` VARCHAR(70) {FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `publ_end`;
ALTER TABLE `{TABLE_PREFIX}sections` CHANGE `title` `title` VARCHAR(70) {FIELD_COLLATION} NOT NULL DEFAULT '';
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `settings`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}settings`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}settings` (
  `name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `value` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`name`)
){TABLE_ENGINE};
-- Tabellenstruktur für Tabelle `users`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}users`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `groups_id` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `password` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `remember_key` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `last_reset` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `email` text{FIELD_COLLATION} NOT NULL,
  `timezone` int(11) NOT NULL DEFAULT '0',
  `date_format` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `time_format` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `language` varchar(5){FIELD_COLLATION} NOT NULL DEFAULT 'DE',
  `home_folder` text{FIELD_COLLATION} NOT NULL,
  `login_when` int(11) NOT NULL DEFAULT '0',
  `login_ip` varchar(15){FIELD_COLLATION} NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`)
){TABLE_ENGINE};
ALTER TABLE `{TABLE_PREFIX}users` ADD `confirm_code` varchar(32){FIELD_COLLATION} NOT NULL DEFAULT '' AFTER `password`;
ALTER TABLE `{TABLE_PREFIX}users` ADD `confirm_timeout` int(11) NOT NULL DEFAULT '0' AFTER `confirm_code`;


