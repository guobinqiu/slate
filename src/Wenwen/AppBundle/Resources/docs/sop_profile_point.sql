CREATE TABLE `sop_profile_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(16) DEFAULT NULL,
  `point_value` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(255) NOT NULL,
  `status_flag` tinyint(4) DEFAULT '1',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_uniq` (`hash`),
  KEY `panelist_status_idx` (`status_flag`,`user_id`),
  KEY `sop_status_idx` (`status_flag`,`id`),
  KEY `updated_at_idx` (`updated_at`),
  KEY `name_user_idx` (`name`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
