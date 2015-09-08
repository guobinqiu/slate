/*
CREATE TABLE `panel_91wenwen_vote` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) DEFAULT NULL,
   `description` text,
   `yyyymm` varchar(10) DEFAULT NULL,
   `start_time` datetime NOT NULL,
   `end_time` datetime NOT NULL,
   `point_value` int(11) DEFAULT NULL,
   `delete_flag` tinyint(4) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=460 DEFAULT CHARSET=utf8

CREATE TABLE `panel_91wenwen_vote_image` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `vote_id` int(11) NOT NULL,
   `filename` varchar(255) DEFAULT NULL,
   `description` varchar(255) DEFAULT NULL,
   `width` int(11) DEFAULT NULL,
   `height` int(11) DEFAULT NULL,
   `sq_path` varchar(255) DEFAULT NULL,
   `sq_width` int(11) DEFAULT NULL,
   `sq_height` int(11) DEFAULT NULL,
   `s_path` varchar(255) DEFAULT NULL,
   `s_width` int(11) DEFAULT NULL,
   `s_height` int(11) DEFAULT NULL,
   `m_path` varchar(255) DEFAULT NULL,
   `m_width` int(11) DEFAULT NULL,
   `m_height` int(11) DEFAULT NULL,
   `delete_flag` tinyint(4) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `91wenwen_vote_uk` (`vote_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=utf8

CREATE TABLE `panel_91wenwen_vote_choice` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `name` varchar(255) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `91wenwen_vote_choice_uk` (`vote_id`,`answer_number`),
   CONSTRAINT `panel_91wenwen_vote_choice_FK_1` FOREIGN KEY (`vote_id`) REFERENCES `panel_91wenwen_vote` (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1735 DEFAULT CHARSET=utf8

CREATE TABLE `panel_91wenwen_vote_answer_201508` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `panelist_id` int(11) NOT NULL,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `panelist_id` (`panelist_id`,`vote_id`),
   KEY `vote_id` (`vote_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `panel_91wenwen_vote_comment_201508` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `panelist_id` int(11) NOT NULL,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `comment_number` int(11) NOT NULL,
   `parent_id` int(11) NOT NULL,
   `clap_count` int(11) NOT NULL,
   `content` text,
   `name` varchar(255) DEFAULT NULL,
   `sex_code` tinyint(4) DEFAULT NULL,
   `age` tinyint(4) DEFAULT NULL,
   `address` varchar(255) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `vote_id` (`vote_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `panel_91wenwen_vote_comment_clap_201508` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `panelist_id` int(11) NOT NULL,
   `comment_id` int(11) NOT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `panelist_id` (`panelist_id`,`comment_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8

 */
 -- comment 功能已经没有，因此不需要comment相关的表
 -- todo: 修改表名

 CREATE TABLE `vote` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) DEFAULT NULL,
   `description` text,
   `yyyymm` varchar(10) DEFAULT NULL,
   `start_time` datetime NOT NULL,
   `end_time` datetime NOT NULL,
   `point_value` int(11) DEFAULT NULL,
   `delete_flag` tinyint(4) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `vote_image` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `vote_id` int(11) NOT NULL,
   `filename` varchar(255) DEFAULT NULL,
   `description` varchar(255) DEFAULT NULL,
   `width` int(11) DEFAULT NULL,
   `height` int(11) DEFAULT NULL,
   `sq_path` varchar(255) DEFAULT NULL,
   `sq_width` int(11) DEFAULT NULL,
   `sq_height` int(11) DEFAULT NULL,
   `s_path` varchar(255) DEFAULT NULL,
   `s_width` int(11) DEFAULT NULL,
   `s_height` int(11) DEFAULT NULL,
   `m_path` varchar(255) DEFAULT NULL,
   `m_width` int(11) DEFAULT NULL,
   `m_height` int(11) DEFAULT NULL,
   `delete_flag` tinyint(4) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `vote_uk` (`vote_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `vote_choice` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `name` varchar(255) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `vote_choice_uk` (`vote_id`,`answer_number`),
   CONSTRAINT `vote_choice_FK_1` FOREIGN KEY (`vote_id`) REFERENCES `vote` (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `vote_answer_yyyymm` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) NOT NULL,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `user_id` (`user_id`,`vote_id`),
   KEY `vote_id` (`vote_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;