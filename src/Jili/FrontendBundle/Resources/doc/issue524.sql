-- 找宝箱游戏

INSERT INTO `ad_category` ( `id` , `category_name` , `asp` , `display_name` ) VALUES (30 , 'game', '91jili', '游戏寻宝箱');


DROP TABLE IF EXISTS `game_seeker_daily`;

CREATE TABLE `game_seeker_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `clicked_day` date DEFAULT NULL COMMENT 'YYYY-mm-dd',
  `token` varchar(32) NOT NULL COMMENT '每次请求重新生成',
  `token_updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `uid_daily` (`user_id`,`clicked_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='寻宝完成状态表';

DROP TABLE IF EXISTS `game_seeker_points_pool`;
CREATE TABLE `game_seeker_points_pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(8) NOT NULL COMMENT '每次发放的积分',
  `send_frequency` int(4) NOT NULL COMMENT '发放的频率',
  `is_published` tinyint(1) NOT NULL COMMENT '是否发布',
  `is_valid` tinyint(1) NOT NULL COMMENT '是否生效',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  PRIMARY KEY (`id`),
  KEY `pts_freq` (`points`,`send_frequency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='寻宝积分管理表';

