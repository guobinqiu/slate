--增加记录
INSERT INTO `jili_db`.`points_exchange_type` (`id` ,`type`)VALUES ('5', '流量包');
INSERT INTO `ad_category` (`id` ,`category_name` ,`asp` ,`display_name`)VALUES ('24', NULL, NULL , '流量包');

--增加表exchange_flow_order
DROP TABLE IF EXISTS `exchange_flow_order`;
CREATE TABLE IF NOT EXISTS `exchange_flow_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'USER ID',
  `exchange_id` int(11) DEFAULT '0' COMMENT 'points_exchange.id',
  `provider` varchar(16) NOT NULL COMMENT '手机号码所属运营商(移动、联通、电信)',
  `province` varchar(64) NOT NULL COMMENT '手机号码归属省份',
  `custom_product_id` varchar(5) NOT NULL COMMENT '流量包产品编码',
  `packagesize` varchar(8) NOT NULL COMMENT '流量包产品大小，如30表示30MB',
  `custom_prise` decimal(8,3) NOT NULL COMMENT '流量包产品用户执行的价格，单位（元）',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户兑换流量订单' AUTO_INCREMENT=1 ;

--增加表flow_order_api_return
DROP TABLE IF EXISTS `flow_order_api_return`;
CREATE TABLE IF NOT EXISTS `flow_order_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` datetime NOT NULL,
  `content` longtext NOT NULL COMMENT '推送内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值状态推送日志' AUTO_INCREMENT=1 

--修改表结构
ALTER TABLE `exchange_danger` ADD `created_at` DATETIME NULL ;