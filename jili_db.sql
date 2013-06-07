-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 05 月 24 日 11:56
-- 服务器版本: 5.5.16
-- PHP 版本: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `jili_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `advertiserment`
--

CREATE TABLE IF NOT EXISTS `advertiserment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `content` text,
  `imageurl` varchar(250) DEFAULT NULL,
  `icon_image` varchar(250) DEFAULT NULL,
  `incentive_type` int(11) NOT NULL,
  `info` text,
  `category` int(11) NOT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- 表的结构 `adw_access_history`
--

CREATE TABLE IF NOT EXISTS `adw_access_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `access_time` datetime DEFAULT NULL,
  `incentive_type` int(11) NOT NULL,
  `incentive` int(11) NOT NULL,
  `incentive_rate` int(11) NOT NULL COMMENT '返回标示',
  PRIMARY KEY (`id`),
  KEY `fk_adw_access_record_user1` (`user_id`),
  KEY `fk_adw_access_record_advertiserment1` (`ad_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- 表的结构 `ad_category`
--

CREATE TABLE IF NOT EXISTS `ad_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ad_position`
--

CREATE TABLE IF NOT EXISTS `ad_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ad_position_advertiserment1` (`ad_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- 表的结构 `black_users`
--

CREATE TABLE IF NOT EXISTS `black_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blacked_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `callboard`
--

CREATE TABLE IF NOT EXISTS `callboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `content` text,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `limit_ad`
--

CREATE TABLE IF NOT EXISTS `limit_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_limit_ad_advertiserment1` (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `limit_ad_result`
--

CREATE TABLE IF NOT EXISTS `limit_ad_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `limit_ad_id` int(11) NOT NULL,
  `result_incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `login_log`
--

CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_date` datetime DEFAULT NULL,
  `login_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `points_exchange`
--

CREATE TABLE IF NOT EXISTS `points_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `exchange_date` datetime DEFAULT NULL,
  `type` int(11) NOT NULL,
  `target_account` varchar(45) DEFAULT NULL,
  `source_point` int(11) NOT NULL,
  `target_point` int(11) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `points_exchange_type`
--

CREATE TABLE IF NOT EXISTS `points_exchange_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history00`
--

CREATE TABLE IF NOT EXISTS `point_history00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history01`
--

CREATE TABLE IF NOT EXISTS `point_history01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history02`
--

CREATE TABLE IF NOT EXISTS `point_history02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history03`
--

CREATE TABLE IF NOT EXISTS `point_history03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history04`
--

CREATE TABLE IF NOT EXISTS `point_history04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history05`
--

CREATE TABLE IF NOT EXISTS `point_history05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history06`
--

CREATE TABLE IF NOT EXISTS `point_history06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history07`
--

CREATE TABLE IF NOT EXISTS `point_history07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history08`
--

CREATE TABLE IF NOT EXISTS `point_history08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_history09`
--

CREATE TABLE IF NOT EXISTS `point_history09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `point_reason`
--

CREATE TABLE IF NOT EXISTS `point_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `rate_ad`
--

CREATE TABLE IF NOT EXISTS `rate_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `income_rate` int(11) NOT NULL,
  `incentive_rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rate_ad_advertiserment1` (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `rate_ad_result`
--

CREATE TABLE IF NOT EXISTS `rate_ad_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rate_ad_id` int(11) NOT NULL,
  `result_price` int(11) NOT NULL,
  `result_incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rate_ad_result_rate_ad1` (`rate_ad_id`),
  KEY `fk_rate_ad_result_user1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_password_code`
--

CREATE TABLE IF NOT EXISTS `set_password_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(45) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `is_available` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(25) DEFAULT NULL,
  `pwd` varchar(45) DEFAULT NULL,
  `sex` int(1) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `is_email_confirmed` int(11) DEFAULT NULL,
  `tel` varchar(45) DEFAULT NULL,
  `is_tel_confirmed` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `education` int(11) DEFAULT NULL COMMENT '学历',
  `profession` int(11) DEFAULT NULL COMMENT '职业',
  `hobby` int(11) DEFAULT NULL COMMENT '爱好',
  `personalDes` text COMMENT '个性说明',
  `identity_num` varchar(40) DEFAULT NULL,
  `register_date` datetime DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_ip` varchar(20) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  `is_info_set` int(11) NOT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 限制导出的表
--

--
-- 限制表 `adw_access_history`
--
ALTER TABLE `adw_access_history`
  ADD CONSTRAINT `fk_adw_access_record_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_adw_access_record_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `ad_position`
--
ALTER TABLE `ad_position`
  ADD CONSTRAINT `fk_ad_position_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `login_log`
--
ALTER TABLE `login_log`
  ADD CONSTRAINT `fk_login_log_user1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `points_exchange`
--
ALTER TABLE `points_exchange`
  ADD CONSTRAINT `fk_points_exchange_user1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `points_exchange_type`
--
ALTER TABLE `points_exchange_type`
  ADD CONSTRAINT `fk_points_exchange_type_points_exchange1` FOREIGN KEY (`id`) REFERENCES `points_exchange` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `point_history00`
--
ALTER TABLE `point_history00`
  ADD CONSTRAINT `fk_point_history_00_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `rate_ad`
--
ALTER TABLE `rate_ad`
  ADD CONSTRAINT `fk_rate_ad_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `rate_ad_result`
--
ALTER TABLE `rate_ad_result`
  ADD CONSTRAINT `fk_rate_ad_result_rate_ad1` FOREIGN KEY (`rate_ad_id`) REFERENCES `rate_ad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rate_ad_result_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_black_users1` FOREIGN KEY (`id`) REFERENCES `black_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
