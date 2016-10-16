-- --------------------------------------------------------
-- Hôte :                        127.0.0.1
-- Version du serveur:           5.7.9 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Export de la structure de la base pour twicecast

-- Export de la structure de table twicecast. category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. channel
CREATE TABLE IF NOT EXISTS `channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_owner` int(10) unsigned NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `fk_spoken_language` int(10) unsigned NOT NULL,
  `fk_project_view_level` tinyint(3) unsigned NOT NULL,
  `fk_project_edit_level` tinyint(3) unsigned NOT NULL,
  `fk_chat_level` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_channel_user` (`fk_owner`),
  KEY `FK_channel_rank` (`fk_project_view_level`),
  KEY `FK_channel_rank_2` (`fk_project_edit_level`),
  KEY `FK_channel_rank_3` (`fk_chat_level`),
  KEY `FK_channel_language` (`fk_spoken_language`),
  CONSTRAINT `FK_channel_language` FOREIGN KEY (`fk_spoken_language`) REFERENCES `language` (`id`),
  CONSTRAINT `FK_channel_rank` FOREIGN KEY (`fk_project_view_level`) REFERENCES `rank` (`id`),
  CONSTRAINT `FK_channel_rank_2` FOREIGN KEY (`fk_project_edit_level`) REFERENCES `rank` (`id`),
  CONSTRAINT `FK_channel_rank_3` FOREIGN KEY (`fk_chat_level`) REFERENCES `rank` (`id`),
  CONSTRAINT `FK_channel_user` FOREIGN KEY (`fk_owner`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. channel_categories
CREATE TABLE IF NOT EXISTS `channel_categories` (
  `fk_channel` int(10) unsigned NOT NULL,
  `fk_category` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_index` (`fk_channel`,`fk_category`),
  KEY `FK_channel_categories_category` (`fk_category`),
  CONSTRAINT `FK_channel_categories_category` FOREIGN KEY (`fk_category`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_channel_categories_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. channel_ranks
CREATE TABLE IF NOT EXISTS `channel_ranks` (
  `fk_channel` int(10) unsigned NOT NULL,
  `fk_user` int(10) unsigned NOT NULL,
  `fk_rank` tinyint(3) unsigned NOT NULL,
  UNIQUE KEY `unique_index` (`fk_channel`,`fk_user`),
  KEY `FK_channel_ranks_user` (`fk_user`),
  KEY `FK_channel_ranks_rank` (`fk_rank`),
  CONSTRAINT `FK_channel_ranks_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`),
  CONSTRAINT `FK_channel_ranks_rank` FOREIGN KEY (`fk_rank`) REFERENCES `rank` (`id`),
  CONSTRAINT `FK_channel_ranks_user` FOREIGN KEY (`fk_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. country
CREATE TABLE IF NOT EXISTS `country` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. language
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. rank
CREATE TABLE IF NOT EXISTS `rank` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. replay
CREATE TABLE IF NOT EXISTS `replay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_channel` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `length` double unsigned NOT NULL,
  `fk_spoken_language` int(10) unsigned NOT NULL,
  `fk_visibility` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_replay_rank` (`fk_visibility`),
  KEY `FK_replay_channel` (`fk_channel`),
  KEY `FK_replay_language` (`fk_spoken_language`),
  CONSTRAINT `FK_replay_channel` FOREIGN KEY (`fk_channel`) REFERENCES `channel` (`id`),
  CONSTRAINT `FK_replay_language` FOREIGN KEY (`fk_spoken_language`) REFERENCES `language` (`id`),
  CONSTRAINT `FK_replay_rank` FOREIGN KEY (`fk_visibility`) REFERENCES `rank` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. replay_categories
CREATE TABLE IF NOT EXISTS `replay_categories` (
  `fk_replay` int(10) unsigned NOT NULL,
  `fk_category` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique_index` (`fk_replay`,`fk_category`),
  KEY `FK_replay_categories_category` (`fk_category`),
  CONSTRAINT `FK_replay_categories_category` FOREIGN KEY (`fk_category`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_replay_categories_replay` FOREIGN KEY (`fk_replay`) REFERENCES `replay` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. session_token
CREATE TABLE IF NOT EXISTS `session_token` (
  `token` varchar(20) NOT NULL,
  `fk_user` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NOT NULL,
  PRIMARY KEY (`token`),
  KEY `FK_session_token_user` (`fk_user`),
  CONSTRAINT `FK_session_token_user` FOREIGN KEY (`fk_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Store all tokens generated for sessions';

-- L'exportation de données n'était pas sélectionnée.


-- Export de la structure de table twicecast. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `fk_country` smallint(5) unsigned NOT NULL,
  `birthdate` date NOT NULL,
  `fk_rank` tinyint(3) unsigned NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_nickname` (`nickname`),
  KEY `FK_user_rank` (`fk_rank`),
  KEY `FK_user_country` (`fk_country`),
  CONSTRAINT `FK_user_country` FOREIGN KEY (`fk_country`) REFERENCES `country` (`id`),
  CONSTRAINT `FK_user_rank` FOREIGN KEY (`fk_rank`) REFERENCES `rank` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- L'exportation de données n'était pas sélectionnée.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
