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

--
-- Daten für Tabelle `datarecord`
--

INSERT INTO `datarecord` (`id`, `key1`, `key2`, `key3`) VALUES
(1, 'value1', 'value2', 'value3'),
(2, 'value1', 'newvalue2', 'newvalue3'),
(3, 'value1', 'value2', 'value3'),
(4, 'value1', 'newvalue2', 'newvalue3'),
(5, 'value1', 'value2', 'value3'),
(6, 'value1', 'newvalue2', 'newvalue3'),
(7, 'value1', 'newvalue2', 'newvalue3'),
(8, 'value1', 'newvalue2', 'newvalue3'),
(14, 'value1', 'newvalue2', 'newvalue3');

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

--
-- Daten für Tabelle `groups`
--

INSERT INTO `groups` (`id`, `groupname`, `system`, `mail`) VALUES
(1, 'root', 1, ''),
(37, 'username2', 0, ''),
(36, 'username1', 0, ''),
(340, 'user', 0, ''),
(339, 'username', 0, '');

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

--
-- Daten für Tabelle `passwd`
--

INSERT INTO `passwd` (`id`, `username`, `mail`, `groups`, `password`, `session`, `ip_login`, `ip_during_registration`, `port_during_registration`, `logintime`, `loginexpires`, `activation`, `data`, `status`, `firstname`, `lastname`, `device_during_registration`, `home`, `profilepicture`) VALUES
(232, 'user', 'mail@mail.de', 'user,', '', '', '', '', '', '', '', 'be9c0a3d41156913d36cf9715ccf63ae', '', '', 'firstname', 'lastname', '', 'frontend_UserManagement.php', ''),
(231, 'username', 'mail@mail.de', 'username,', '5f4dcc3b5aa765d61d8327deb882cf99', '5217840915ed98901b610f61132a6c56', '::1', '', '', '1379513451', '1381313451', '', '', '', '', '', '', 'UserManagement_frontend.php', '');

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

--
-- Daten für Tabelle `translations`
--

INSERT INTO `translations` (`id`, `keyword`, `en`, `de`, `ru`, `es`) VALUES
(1, 'hello', 'hello', 'hallo', 'nastrovie', '!ola'),
(2, 'password forgotten?', 'password forgotten?', 'Passwort vergessen?', 'забыли пароль?', '¿Olvidó su contraseña?'),
(3, 'Please mail me a new password.', 'Please mail me a new password.', 'Bitte neues Passwort zusenden.', '', ''),
(4, 'New password for', 'New password for', 'Neues Passwort für', '', ''),
(5, 'Your new password for', 'Your new password for', 'Ihr neues Passwort für', '', ''),
(6, 'Password', 'Password', 'Passwort', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
