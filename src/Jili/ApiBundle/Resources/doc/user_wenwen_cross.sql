--
-- 表的结构 `user_wenwen_cross`
--
CREATE TABLE IF NOT EXISTS `user_wenwen_cross` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 表的结构 `user_wenwen_cross_token`
--
CREATE TABLE IF NOT EXISTS `user_wenwen_cross_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cross_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cross_id` (`cross_id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;