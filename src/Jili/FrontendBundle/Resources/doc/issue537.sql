-- 找宝箱游戏

INSERT INTO `ad_category` ( `id` , `category_name` , `asp` , `display_name` ) VALUES (31 , 'game', '91jili', '游戏砸金蛋');

DROP TABLE IF EXISTS `game_eggs_breaker_taobao_order` ;
CREATE TABLE `game_eggs_breaker_taobao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `order_at` date NOT NULL,
  `order_paid` float(9,2) NOT NULL DEFAULT '0.00',
  `audit_by` varchar(16) DEFAULT NULL,
  `audit_status` int(2) NOT NULL DEFAULT 0, -- 0 init, 1 pending ,2 completed 
  `audit_pended_at` datetime NULL,
  `is_valid` int(2) NOT NULL DEFAULT 0, -- 0 init ,1 valid ,2 not valid  ,3  
  `is_egged` int(2) NOT NULL DEFAULT 0, -- 0, 1, 2 
  `updated_at` datetime NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_order` (`user_id`,`order_id`),
  KEY `audit_pend` (`audit_status`,`audit_pended_at`) -- find pending cron
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `game_eggs_breaker_eggs_info` ;
CREATE TABLE `game_eggs_breaker_eggs_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `offcut_for_next` float(9,2) NOT NULL DEFAULT '0.00',
  `num_of_common` int(11) NOT NULL,
  `num_of_consolation` int(11) NOT NULL,
  `num_updated_at` datetime NULL,
  `token` varchar(32) NOT NULL,
  `token_updated_at` datetime NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user_id`) ,
  KEY `user_visit_token` (`user_id`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `game_eggs_broken_log` ;
CREATE TABLE `game_eggs_broken_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `egg_type` int(2) NOT NULL DEFAULT 0, -- 0, 1:,2 
  `points_acquried` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

