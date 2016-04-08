CREATE TABLE `user_deleted` (
  `user_id` int(11) NOT NULL,
  `reason` text,
  `user_info` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;