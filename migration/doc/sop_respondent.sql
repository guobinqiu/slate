CREATE TABLE IF NOT EXISTS `sop_respondent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status_flag` tinyint(4) DEFAULT '1',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_uniq` (`user_id`),
  KEY `user_status_idx` (`status_flag`,`user_id`),
  KEY `sop_status_idx` (`status_flag`,`id`),
  KEY `updated_at_idx` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
