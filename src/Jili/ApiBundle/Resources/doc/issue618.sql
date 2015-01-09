-- 点击表
CREATE TABLE `activity_gathering_checkin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `checkin_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `activity_gathering_checkin_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 订单表
CREATE TABLE `activity_gathering_taobao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_identity` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_order_user` (`order_identity`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_gathering_taobao_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
