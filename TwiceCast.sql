-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 28 Janvier 2017 à 09:36
-- Version du serveur :  5.7.9
-- Version de PHP :  7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `twicecast`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `channel`
--

DROP TABLE IF EXISTS `channel`;
CREATE TABLE IF NOT EXISTS `channel` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_owner` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `fk_spoken_language` int(10) UNSIGNED NOT NULL,
  `fk_project_view_level` tinyint(3) UNSIGNED NOT NULL,
  `fk_project_edit_level` tinyint(3) UNSIGNED NOT NULL,
  `fk_chat_level` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_channel_user` (`fk_owner`),
  KEY `FK_channel_rank` (`fk_project_view_level`),
  KEY `FK_channel_rank_2` (`fk_project_edit_level`),
  KEY `FK_channel_rank_3` (`fk_chat_level`),
  KEY `FK_channel_language` (`fk_spoken_language`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `channel`
--

INSERT INTO `channel` (`id`, `fk_owner`, `title`, `description`, `fk_spoken_language`, `fk_project_view_level`, `fk_project_edit_level`, `fk_chat_level`) VALUES
(1, 1, 'Chanel', 'Chanel de test', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `channel_categories`
--

DROP TABLE IF EXISTS `channel_categories`;
CREATE TABLE IF NOT EXISTS `channel_categories` (
  `fk_channel` int(10) UNSIGNED NOT NULL,
  `fk_category` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `unique_index` (`fk_channel`,`fk_category`),
  KEY `FK_channel_categories_category` (`fk_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `channel_ranks`
--

DROP TABLE IF EXISTS `channel_ranks`;
CREATE TABLE IF NOT EXISTS `channel_ranks` (
  `fk_channel` int(10) UNSIGNED NOT NULL,
  `fk_user` int(10) UNSIGNED NOT NULL,
  `fk_rank` tinyint(3) UNSIGNED NOT NULL,
  UNIQUE KEY `unique_index` (`fk_channel`,`fk_user`),
  KEY `FK_channel_ranks_user` (`fk_user`),
  KEY `FK_channel_ranks_rank` (`fk_rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `country`
--

INSERT INTO `country` (`id`, `code`, `name`) VALUES
(0, 'NO', 'NONE'),
(1, 'FRA', 'France'),
(2, 'GBR', 'Royaume-Uni'),
(3, 'USA', 'États-Unis');

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `language`
--

INSERT INTO `language` (`id`, `name`) VALUES
(1, 'Français');

-- --------------------------------------------------------

--
-- Structure de la table `rank`
--

DROP TABLE IF EXISTS `rank`;
CREATE TABLE IF NOT EXISTS `rank` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `rank`
--

INSERT INTO `rank` (`id`, `title`) VALUES
(0, 'NONE'),
(1, 'Private'),
(2, 'Private'),
(3, 'Corporal'),
(4, 'Sergeant'),
(5, 'Master Sergeant'),
(6, 'Sergeant Major'),
(7, 'Knight'),
(8, 'Knight-Lieutenant'),
(9, 'Knight-Captain'),
(10, 'Knight-Champion'),
(11, 'Lieutenant Commander'),
(12, 'Commander'),
(13, 'Marshal'),
(14, 'Field Marshal'),
(15, 'Grand Marshal');

-- --------------------------------------------------------

--
-- Structure de la table `replay`
--

DROP TABLE IF EXISTS `replay`;
CREATE TABLE IF NOT EXISTS `replay` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fk_channel` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `length` double UNSIGNED NOT NULL,
  `fk_spoken_language` int(10) UNSIGNED NOT NULL,
  `fk_visibility` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_replay_rank` (`fk_visibility`),
  KEY `FK_replay_channel` (`fk_channel`),
  KEY `FK_replay_language` (`fk_spoken_language`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `replay`
--

INSERT INTO `replay` (`id`, `fk_channel`, `name`, `description`, `length`, `fk_spoken_language`, `fk_visibility`) VALUES
(2, 1, 'TestingReplay', 'Ceci est un replay de test', 0.1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `replay_categories`
--

DROP TABLE IF EXISTS `replay_categories`;
CREATE TABLE IF NOT EXISTS `replay_categories` (
  `fk_replay` int(10) UNSIGNED NOT NULL,
  `fk_category` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `unique_index` (`fk_replay`,`fk_category`),
  KEY `FK_replay_categories_category` (`fk_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `session_token`
--

DROP TABLE IF EXISTS `session_token`;
CREATE TABLE IF NOT EXISTS `session_token` (
  `token` varchar(20) NOT NULL,
  `fk_user` int(10) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NOT NULL,
  PRIMARY KEY (`token`),
  KEY `FK_session_token_user` (`fk_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Store all tokens generated for sessions';

-- --------------------------------------------------------

--
-- Structure de la table `stream`
--

DROP TABLE IF EXISTS `stream`;
CREATE TABLE IF NOT EXISTS `stream` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `fk_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `stream`
--

INSERT INTO `stream` (`id`, `title`, `fk_user`) VALUES
(1, 'Stream1', 5),
(2, 'Stream2', 5),
(3, 'Stream3', 5),
(4, 'Stream4', 5);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `fk_country` smallint(5) UNSIGNED NOT NULL,
  `birthdate` date DEFAULT NULL,
  `fk_rank` tinyint(3) UNSIGNED NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_nickname` (`nickname`),
  KEY `FK_user_rank` (`fk_rank`),
  KEY `FK_user_country` (`fk_country`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `nickname`, `fk_country`, `birthdate`, `fk_rank`, `register_date`, `last_visit_date`) VALUES
(1, 'email@example.fr', 'hash', 'Guest1', 1, '2016-10-02', 2, '2016-10-16 19:10:16', '2016-11-14 17:04:58'),
(2, 'email@example.gb', 'hash', 'Guest2', 2, '2016-10-01', 2, '2016-10-16 19:10:16', '2016-11-14 22:28:15'),
(5, 'email@email.com', 'aplzpelapzelapzel', 'TestingUser', 0, NULL, 14, '2016-11-14 17:55:58', '2016-11-14 17:55:58'),
(7, 'FXZdqphq9Q@email.com', '3LPcAEo0', 'zeYEX8', 0, NULL, 0, '2016-12-09 12:35:37', '2016-12-09 12:35:37'),
(8, 'GazX49Irea@email.com', '72KlGtgk', 'WoHn9E', 0, NULL, 0, '2016-12-14 22:33:54', '2016-12-14 22:33:54'),
(9, 'cOzIF4Zui3@email.com', 'gnzPwZSd', '9nG40M', 0, NULL, 0, '2017-01-13 17:51:30', '2017-01-13 17:51:30');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `channel`
--
ALTER TABLE `channel`
  ADD CONSTRAINT `FK_channel_language` FOREIGN KEY (`fk_spoken_language`) REFERENCES `language` (`id`),
  ADD CONSTRAINT `FK_channel_rank` FOREIGN KEY (`fk_project_view_level`) REFERENCES `rank` (`id`),
  ADD CONSTRAINT `FK_channel_rank_2` FOREIGN KEY (`fk_project_edit_level`) REFERENCES `rank` (`id`),
  ADD CONSTRAINT `FK_channel_rank_3` FOREIGN KEY (`fk_chat_level`) REFERENCES `rank` (`id`),
  ADD CONSTRAINT `FK_channel_user` FOREIGN KEY (`fk_owner`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `channel_categories`
--
ALTER TABLE `channel_categories`
  ADD CONSTRAINT `FK_channel_categories_category` FOREIGN KEY (`fk_category`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_channel_categories_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`);

--
-- Contraintes pour la table `channel_ranks`
--
ALTER TABLE `channel_ranks`
  ADD CONSTRAINT `FK_channel_ranks_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`),
  ADD CONSTRAINT `FK_channel_ranks_rank` FOREIGN KEY (`fk_rank`) REFERENCES `rank` (`id`),
  ADD CONSTRAINT `FK_channel_ranks_user` FOREIGN KEY (`fk_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `replay`
--
ALTER TABLE `replay`
  ADD CONSTRAINT `FK_replay_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`),
  ADD CONSTRAINT `FK_replay_language` FOREIGN KEY (`fk_spoken_language`) REFERENCES `language` (`id`),
  ADD CONSTRAINT `FK_replay_rank` FOREIGN KEY (`fk_visibility`) REFERENCES `rank` (`id`);

--
-- Contraintes pour la table `replay_categories`
--
ALTER TABLE `replay_categories`
  ADD CONSTRAINT `FK_replay_categories_category` FOREIGN KEY (`fk_category`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_replay_categories_replay` FOREIGN KEY (`fk_replay`) REFERENCES `replay` (`id`);

--
-- Contraintes pour la table `session_token`
--
ALTER TABLE `session_token`
  ADD CONSTRAINT `FK_session_token_user` FOREIGN KEY (`fk_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_user_country` FOREIGN KEY (`fk_country`) REFERENCES `country` (`id`),
  ADD CONSTRAINT `FK_user_rank` FOREIGN KEY (`fk_rank`) REFERENCES `rank` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
