CREATE TABLE IF NOT EXISTS `market_activity_click_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `market_activity_id` int(11) NOT NULL,
  `click_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家活动点击数' AUTO_INCREMENT=1 ;