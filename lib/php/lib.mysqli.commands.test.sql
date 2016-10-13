-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 06:48 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--
DROP DATABASE `test`;
CREATE DATABASE `test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `test`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `datarecord`
--

CREATE TABLE IF NOT EXISTS `datarecord` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key1` text NOT NULL,
  `key2` text NOT NULL,
  `key3` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` text NOT NULL,
  `system` tinyint(1) NOT NULL COMMENT 'if this is a default system group, that should never be deleted',
  `mail` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=341 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `passwd`
--

CREATE TABLE IF NOT EXISTS `passwd` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `mail` text NOT NULL,
  `groups` text NOT NULL COMMENT 'list of groups the user belongs to',
  `password` text NOT NULL,
  `session` varchar(255) NOT NULL COMMENT 'random session id',
  `ip_login` varchar(255) NOT NULL COMMENT 'login-ip that user had during login',
  `ip_during_registration` text NOT NULL,
  `port_during_registration` text NOT NULL,
  `logintime` varchar(255) NOT NULL COMMENT 'server-timestamp when user logged in',
  `loginexpires` varchar(255) NOT NULL COMMENT 'server-timestamp when session expires',
  `activation` varchar(255) NOT NULL COMMENT 'activation id',
  `data` text NOT NULL COMMENT 'additional data about the user',
  `status` varchar(255) NOT NULL COMMENT 'the state of the user active, disabled, deleted',
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `device_during_registration` text NOT NULL,
  `home` text NOT NULL,
  `profilepicture` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='stores users, passwords and sessions' AUTO_INCREMENT=233 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `keyword` text NOT NULL,
  `en` text NOT NULL,
  `de` text NOT NULL,
  `ru` text NOT NULL,
  `es` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
