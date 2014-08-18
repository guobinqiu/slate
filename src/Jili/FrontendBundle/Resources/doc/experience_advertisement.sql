--
-- 表的结构 `experience_advertisement`
--

CREATE TABLE IF NOT EXISTS `experience_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_hall` int(1) NOT NULL DEFAULT '1' COMMENT '1 任务大厅1\n2 任务大厅2\n',
  `point` int(11) DEFAULT NULL COMMENT '米粒',
  `mission_img_url` varchar(250) DEFAULT NULL COMMENT '任务图片链接',
  `mission_title` varchar(250) DEFAULT NULL COMMENT '任务标题',
  `delete_flag` int(1) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

