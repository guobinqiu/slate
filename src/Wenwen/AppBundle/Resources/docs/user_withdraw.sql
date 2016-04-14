CREATE TABLE IF NOT EXISTS `user_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reason` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `user_deleted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_from_wenwen` int(11) DEFAULT NULL,
  `wenwen_user` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `nick` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` int(11) DEFAULT NULL,
  `birthday` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_email_confirmed` int(11) DEFAULT NULL,
  `tel` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_tel_confirmed` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `education` int(11) DEFAULT NULL,
  `profession` int(11) DEFAULT NULL,
  `income` int(11) DEFAULT NULL,
  `hobby` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personalDes` longtext COLLATE utf8_unicode_ci,
  `identity_num` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reward_multiple` double NOT NULL,
  `register_date` datetime DEFAULT NULL,
  `register_complete_date` datetime DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `points` int(11) NOT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  `is_info_set` int(11) NOT NULL,
  `icon_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uniqkey` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_created_at` datetime DEFAULT NULL,
  `origin_flag` smallint(6) DEFAULT NULL,
  `created_remote_addr` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_user_agent` longtext COLLATE utf8_unicode_ci,
  `campaign_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_choice` smallint(6) DEFAULT NULL,
  `fav_music` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `monthly_wish` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `industry_code` int(11) DEFAULT NULL,
  `work_section_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `user_wenwen_login_deleted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `login_password_salt` longtext COLLATE utf8_unicode_ci,
  `login_password_crypt_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_password` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
