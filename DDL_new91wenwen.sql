-- MySQL dump 10.13  Distrib 5.6.10, for Linux (x86_64)
--
-- Host: localhost    Database: zili_dev_3
-- ------------------------------------------------------
-- Server version	5.6.10-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `91ww_user_token`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `91ww_user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `token` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_gathering_taobao_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_gathering_taobao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_identity` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_order` (`user_id`,`order_identity`),
  CONSTRAINT `activity_gathering_taobao_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_activity`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(127) DEFAULT NULL COMMENT '活动标题',
  `description` varchar(255) DEFAULT NULL COMMENT '活动内容描述',
  `started_at` datetime NOT NULL COMMENT '活动开始时间',
  `finished_at` datetime NOT NULL COMMENT '结束时间',
  `percentage` float(7,2) NOT NULL DEFAULT '1.00' COMMENT '比例。default: 100%',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: 失效; 0: 有效',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 显示; 1: 隐藏',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_banner`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon_image` varchar(250) DEFAULT NULL,
  `ad_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) DEFAULT NULL,
  `asp` varchar(64) DEFAULT NULL COMMENT '平台供应商',
  `display_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_position`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ad_position_advertiserment1` (`ad_id`),
  CONSTRAINT `fk_ad_position_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `advertiserment`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advertiserment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(250) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL COMMENT 'emar  action_id',
  `created_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `decription` varchar(1000) DEFAULT NULL,
  `content` text,
  `imageurl` varchar(250) DEFAULT NULL,
  `is_expired` tinyint(1) DEFAULT '0' COMMENT 'imageurl response reports expired',
  `icon_image` varchar(250) DEFAULT NULL,
  `list_image` varchar(250) DEFAULT NULL,
  `incentive_type` int(1) DEFAULT NULL,
  `incentive_rate` int(6) DEFAULT NULL,
  `reward_rate` float DEFAULT '30',
  `incentive` int(11) DEFAULT NULL,
  `info` text,
  `category` int(11) DEFAULT '0',
  `delete_flag` int(11) DEFAULT '0',
  `wenwen_user` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adw_access_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adw_access_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `access_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_adw_access_record_user1` (`user_id`),
  KEY `fk_adw_access_record_advertiserment1` (`ad_id`),
  CONSTRAINT `fk_adw_access_record_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adw_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adw_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` datetime NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adw_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adw_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `adw_return_time` datetime DEFAULT NULL,
  `confirm_time` datetime DEFAULT NULL,
  `happen_time` datetime DEFAULT NULL,
  `incentive_rate` int(6) DEFAULT NULL,
  `incentive_type` int(1) DEFAULT NULL,
  `incentive` int(11) DEFAULT NULL,
  `comm` float DEFAULT NULL,
  `ocd` varchar(100) DEFAULT NULL,
  `order_price` float DEFAULT NULL,
  `order_status` int(2) NOT NULL DEFAULT '0',
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  `order_type` int(11) DEFAULT NULL COMMENT '2:合并后的order',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amazon_coupon`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amazon_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `coupon_od` varchar(50) DEFAULT NULL,
  `coupon_elec` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bangwoya_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bangwoya_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bangwoya_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bangwoya_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `tid` varchar(100) DEFAULT NULL,
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `black_users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `black_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blacked_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `callboard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `content` text,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `cb_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cb_category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cb_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chanet_advertisement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chanet_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `ads_name` varchar(64) NOT NULL COMMENT '活动名称',
  `category` varchar(128) NOT NULL COMMENT '活动分类',
  `ads_url_type` varchar(128) NOT NULL COMMENT '链接类型',
  `ads_url` varchar(128) NOT NULL COMMENT '首页地址',
  `marketing_url` text NOT NULL COMMENT '推广链接',
  `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL COMMENT '1: 使用中, 0: 不在使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chanet_commission`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chanet_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL COMMENT '1: 使用中, 0: 不在使用',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chanet_commission_data`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chanet_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `commission` varchar(100) DEFAULT NULL COMMENT '佣金比例',
  `commission_period` varchar(100) DEFAULT NULL COMMENT '有效期',
  `description` text COMMENT '备注',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_adver_list`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_adver_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `inter_space` int(11) NOT NULL COMMENT '间隙',
  `operation_method` int(11) DEFAULT '0' COMMENT '3: manual, 5:auto, 0 or 15: all ',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的商店列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_click_list`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_click_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `click_date` varchar(20) NOT NULL,
  `open_shop_times` int(11) NOT NULL COMMENT '点击的数量',
  `status` int(11) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的点击总数';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_click_list_bk`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_click_list_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `click_date` varchar(20) NOT NULL,
  `open_shop_times` int(11) NOT NULL COMMENT '点击的数量',
  `status` int(11) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的点击总数';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_point_times`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_point_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `point_times` int(11) NOT NULL,
  `checkin_type` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的积分倍数';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_user_list`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_user_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `click_date` varchar(20) NOT NULL,
  `open_shop_id` int(11) NOT NULL COMMENT '对应 checkin_adver_list的id',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的用户列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkin_user_list_bk`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkin_user_list_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `click_date` varchar(20) NOT NULL,
  `open_shop_id` int(11) NOT NULL COMMENT '对应 checkin_adver_list的id',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到的用户列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cint_permission`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cint_permission` (
  `user_id` int(11) NOT NULL,
  `permission_flag` tinyint(4) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cint_research_survey_participation_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cint_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cint_project_id` int(11) NOT NULL,
  `cint_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cint_project_member_uniq` (`cint_project_id`,`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cint_user_agreement_participation_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cint_user_agreement_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `agreement_status` int(11) NOT NULL DEFAULT '0',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_uniq_key` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cityList`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cityList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cityName` varchar(50) NOT NULL,
  `provinceId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cps_advertisement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cps_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_category_id` int(11) NOT NULL COMMENT 'FK to ad_category',
  `ad_id` int(11) NOT NULL COMMENT 'FK to XXX_advertisement',
  `title` varchar(64) NOT NULL COMMENT '活动名称',
  `marketing_url` text NOT NULL COMMENT '推广链接,(cps平台的url)',
  `ads_url` varchar(128) NOT NULL COMMENT '活动目标地址',
  `commission` varchar(100) DEFAULT '' COMMENT '返利详情',
  `website_name` varchar(64) NOT NULL COMMENT '商家名称',
  `website_name_dictionary_key` char(1) NOT NULL DEFAULT '' COMMENT '商家名称索引',
  `website_category` varchar(128) NOT NULL COMMENT '活动分类',
  `website_host` varchar(128) NOT NULL COMMENT '活动地址(商家名)的域名，用于找logo',
  `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
  `is_activated` int(2) NOT NULL DEFAULT '0' COMMENT '1: 使用中, 0: 不在使用 , 2: 丢弃',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_id` (`ad_category_id`,`ad_id`,`is_activated`),
  UNIQUE KEY `website_host` (`website_host`,`is_activated`),
  KEY `website_name_dictionary_key` (`website_name_dictionary_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duomai_advertisement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duomai_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `ads_name` varchar(64) NOT NULL COMMENT '活动名称',
  `ads_url` varchar(128) NOT NULL COMMENT '网址',
  `ads_commission` varchar(64) NOT NULL COMMENT '佣金',
  `start_time` date NOT NULL COMMENT '活动时间(起)',
  `end_time` date NOT NULL COMMENT '活动时间(止)',
  `category` varchar(128) NOT NULL COMMENT '活动分类',
  `return_day` int(2) NOT NULL DEFAULT '0' COMMENT '效果认定期RD',
  `billing_cycle` varchar(255) NOT NULL COMMENT '结算周期',
  `link_custom` varchar(128) NOT NULL COMMENT '自定义链接',
  `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL DEFAULT '0' COMMENT '1: 使用中, 0: 不在使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duomai_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duomai_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duomai_commission`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duomai_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL DEFAULT '0' COMMENT '1: 使用中, 0: 不在使用',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duomai_commission_data`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duomai_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `commission` varchar(100) DEFAULT NULL COMMENT '佣金比例',
  `commission_period` varchar(100) DEFAULT NULL COMMENT '有效期',
  `description` text COMMENT '备注',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `duomai_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `duomai_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'euid网站主设定的反馈标签',
  `ocd` varchar(32) NOT NULL COMMENT '请求参数中的id',
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `ads_name` varchar(128) NOT NULL COMMENT '活动名称',
  `site_id` int(11) NOT NULL COMMENT '网站ID',
  `link_id` int(11) NOT NULL COMMENT '活动链接ID',
  `order_sn` varchar(32) NOT NULL COMMENT 'order_sn 订单编号',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '下单时间',
  `orders_price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `comm` float(10,2) NOT NULL DEFAULT '0.00' COMMENT 'siter_commission 订单佣金',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '订单状态  -1 无效 0 未确认 1 确认 2 结算',
  `deactivated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'status= -1 的时间',
  `confirmed_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'status= 1 的时间',
  `balanced_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'status= 2 的时间',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'status= 0 的时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_idx` (`site_id`,`ocd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_access_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_access_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `access_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_emar_access_record_user1` (`user_id`),
  KEY `fk_emar_access_record_advertiserment1` (`ad_id`),
  CONSTRAINT `fk_emar_access_record_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_activity_commission`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_activity_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL COMMENT '活动ID',
  `activity_name` varchar(100) DEFAULT NULL COMMENT '活动名称',
  `activity_category` varchar(100) DEFAULT NULL COMMENT '活动分类',
  `commission_id` int(3) NOT NULL COMMENT '佣金序号',
  `commission_number` varchar(100) DEFAULT NULL COMMENT '佣金编号',
  `commission_name` varchar(200) DEFAULT NULL COMMENT '佣金名称',
  `commission` varchar(100) DEFAULT NULL COMMENT '佣金',
  `commission_period` varchar(100) DEFAULT NULL COMMENT '佣金周期',
  `apply_products` varchar(200) DEFAULT NULL COMMENT '佣金适用商品',
  `description` text COMMENT '说明',
  `mall_name` varchar(100) DEFAULT NULL COMMENT '商城名',
  `rebate_type` int(1) DEFAULT NULL COMMENT '佣金比率类型',
  `rebate` varchar(10) DEFAULT NULL COMMENT '佣金比率',
  PRIMARY KEY (`id`),
  KEY `mall_name` (`mall_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_advertisement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `ads_name` varchar(64) NOT NULL COMMENT '活动名称',
  `category` varchar(128) NOT NULL DEFAULT '' COMMENT '活动分类',
  `commission` varchar(128) NOT NULL DEFAULT '' COMMENT '佣金',
  `commission_period` varchar(100) DEFAULT '' COMMENT '结算周期',
  `ads_url` varchar(128) NOT NULL COMMENT '首页地址',
  `can_customize_target` int(1) NOT NULL DEFAULT '1' COMMENT '是否允许修改目标地址',
  `feedback_tag` varchar(4) NOT NULL DEFAULT 'c' COMMENT '反馈标签',
  `marketing_url` text NOT NULL COMMENT '自定义链接',
  `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL DEFAULT '0' COMMENT '1: 使用中, 0: 不在使用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_commission`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `fixed_hash` varchar(64) NOT NULL COMMENT '更新时使用',
  `is_activated` int(2) NOT NULL DEFAULT '0' COMMENT '1: 使用中, 0: 不在使用',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fixed_hash` (`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_commission_data`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT '' COMMENT '佣金类目',
  `commission` varchar(100) DEFAULT '' COMMENT '佣金',
  `commission_period` varchar(100) DEFAULT '' COMMENT '佣金周期',
  `product_apply_to` varchar(100) DEFAULT '' COMMENT '适用商品',
  `description` text COMMENT '详细说明',
  `created_at` datetime NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `ad_type` varchar(16) NOT NULL DEFAULT 'emar' COMMENT '表示ad_id对应, local: advertiserment, emar: open.yiqifa.ad.get',
  `created_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `happened_at` datetime DEFAULT NULL,
  `comm` float DEFAULT NULL,
  `ocd` varchar(100) DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `adid_ocd_uniq` (`ad_id`,`ocd`),
  KEY `ad_id_ref` (`ad_id`,`ad_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_products_cron`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_products_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '商品id',
  `p_name` varchar(128) DEFAULT '' COMMENT '商品名称',
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_name` varchar(128) DEFAULT '' COMMENT '商家网站名称',
  `ori_price` varchar(128) DEFAULT '0.0' COMMENT '参考价格，原始价格',
  `cur_price` varchar(128) DEFAULT '0.0' COMMENT '实际价格，现在价格',
  `pic_url` varchar(128) DEFAULT '' COMMENT '图片链接',
  `catid` int(11) NOT NULL COMMENT '商品分类id',
  `cname` varchar(128) DEFAULT '' COMMENT '商品分类名称',
  `p_o_url` varchar(128) DEFAULT '' COMMENT '商品的计费链接',
  `short_intro` text COMMENT '商品详情',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pid` (`pid`),
  KEY `wid_catid` (`web_id`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于下载';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_products_croned`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_products_croned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '商品id',
  `p_name` varchar(128) DEFAULT '' COMMENT '商品名称',
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_name` varchar(128) DEFAULT '' COMMENT '商家网站名称',
  `ori_price` varchar(128) DEFAULT '0.0' COMMENT '参考价格，原始价格',
  `cur_price` varchar(128) DEFAULT '0.0' COMMENT '实际价格，现在价格',
  `pic_url` varchar(128) DEFAULT '' COMMENT '图片链接',
  `catid` int(11) NOT NULL COMMENT '商品分类id',
  `cname` varchar(128) DEFAULT '' COMMENT '商品分类名称',
  `p_o_url` varchar(128) DEFAULT '' COMMENT '商品的计费链接',
  `short_intro` text COMMENT '商品详情',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pid` (`pid`),
  KEY `wid_catid` (`web_id`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于查询';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_request`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(16) CHARACTER SET utf8 NOT NULL COMMENT '时间标签，YmdHi',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '对emar api请求的次数',
  `size_up` int(11) NOT NULL DEFAULT '0' COMMENT '对emar api请求 的size之和',
  `size_down` int(11) NOT NULL DEFAULT '0' COMMENT '对emar api返回的size之和',
  `time_consumed_total` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '使用时间之和',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_websites`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_catid` int(11) DEFAULT NULL COMMENT '商家网站所属分类的分类id',
  `commission` varchar(128) DEFAULT '' COMMENT ' 推广佣金比例信息',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '是否已经弃用, 0:末弃用',
  `position` int(11) DEFAULT NULL COMMENT '商家网站显示的顺序',
  `is_hidden` tinyint(1) DEFAULT '1' COMMENT '是否不显示, 1:不用做页面显示',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否为热卖商家',
  `hot_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '热卖商家 排序',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`),
  KEY `web_catid` (`web_catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_websites_category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_websites_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的id',
  `category_id` int(11) NOT NULL COMMENT '商品分类',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '计数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='emar开放接口商品的商家与类型的对应关系,用于查询';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_websites_category_cron`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_websites_category_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的id',
  `category_id` int(11) NOT NULL COMMENT '商品分类',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '计数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='emar开放接口商品的商家与类型的对应关系,用于cron';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_websites_cron`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_websites_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_name` varchar(128) DEFAULT '' COMMENT '商家网站的中文名称',
  `web_catid` int(11) DEFAULT NULL COMMENT '商家网站所属分类的分类id',
  `logo_url` varchar(128) DEFAULT '' COMMENT '网站LOGO图片的URL',
  `web_url` varchar(255) DEFAULT NULL COMMENT '商品的计费链接',
  `information` text COMMENT '商家网站的描述信息',
  `begin_date` varchar(128) DEFAULT '' COMMENT '网站推广开始时间',
  `end_date` varchar(128) DEFAULT '' COMMENT '网站推广结束时间',
  `commission` text COMMENT '推广佣金比例信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于下载';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emar_websites_croned`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emar_websites_croned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_name` varchar(128) DEFAULT '' COMMENT '商家网站的中文名称',
  `web_catid` int(11) DEFAULT NULL COMMENT '商家网站所属分类的分类id',
  `logo_url` varchar(128) DEFAULT '' COMMENT '网站LOGO图片的URL',
  `web_url` varchar(255) DEFAULT NULL COMMENT '商品的计费链接',
  `information` text COMMENT '商家网站的描述信息',
  `begin_date` varchar(128) DEFAULT '' COMMENT '网站推广开始时间',
  `end_date` varchar(128) DEFAULT '' COMMENT '网站推广结束时间',
  `commission` text COMMENT '推广佣金比例信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于查询';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_amazon_result`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_amazon_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange_id` int(11) DEFAULT NULL,
  `amazonCard_one` varchar(50) DEFAULT NULL,
  `amazonCard_two` varchar(50) DEFAULT NULL,
  `amazonCard_three` varchar(50) DEFAULT NULL,
  `amazonCard_four` varchar(50) DEFAULT NULL,
  `amazonCard_five` varchar(50) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_danger`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_danger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `exchange_id` int(11) NOT NULL,
  `danger_type` int(11) NOT NULL COMMENT '1 同一手机 2 相同ip 3同一身份证',
  `danger_content` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_flow_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_flow_order` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户兑换流量订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_from_wenwen`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_from_wenwen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wenwen_exchange_id` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `user_wenwen_cross_id` int(11) DEFAULT NULL,
  `payment_point` int(11) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wenwen_exchange_id` (`wenwen_exchange_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exchange_from_wenwen_back20141015`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exchange_from_wenwen_back20141015` (
  `id` int(11) NOT NULL DEFAULT '0',
  `wenwen_exchange_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `payment_point` int(11) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `reason` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `create_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `experience_advertisement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `experience_advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission_hall` int(1) NOT NULL DEFAULT '1' COMMENT '1 任务大厅1\n2 任务大厅2\n',
  `point` int(11) DEFAULT NULL COMMENT '米粒',
  `mission_img_url` varchar(250) DEFAULT NULL COMMENT '任务图片链接',
  `mission_title` varchar(250) DEFAULT NULL COMMENT '任务标题',
  `delete_flag` int(1) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flow_order_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flow_order_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` longtext NOT NULL COMMENT '推送内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值状态推送日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fulcrum_research_survey_participation_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fulcrum_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fulcrum_project_id` int(11) NOT NULL,
  `fulcrum_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fulcrum_project_member_uniq` (`fulcrum_project_id`,`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fulcrum_user_agreement_participation_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fulcrum_user_agreement_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_member_id` varchar(255) NOT NULL,
  `agreement_status` int(11) NOT NULL DEFAULT '0',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_member_id_uniq_key` (`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_eggs_breaker_eggs_info`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_eggs_breaker_eggs_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_paid` float(9,2) NOT NULL DEFAULT '0.00',
  `offcut_for_next` float(9,2) NOT NULL DEFAULT '0.00',
  `num_of_common` int(11) NOT NULL,
  `num_of_consolation` int(11) NOT NULL,
  `num_updated_at` datetime DEFAULT NULL,
  `token` varchar(32) NOT NULL,
  `token_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user_id`),
  KEY `user_visit_token` (`user_id`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_eggs_breaker_taobao_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_eggs_breaker_taobao_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `order_at` date NOT NULL,
  `order_paid` float(9,2) NOT NULL DEFAULT '0.00',
  `audit_by` varchar(16) DEFAULT NULL,
  `audit_status` int(2) NOT NULL DEFAULT '0',
  `audit_pended_at` datetime DEFAULT NULL,
  `is_valid` int(2) NOT NULL DEFAULT '0',
  `is_egged` int(2) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_order` (`user_id`,`order_id`),
  KEY `audit_pend` (`audit_status`,`audit_pended_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_eggs_broken_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_eggs_broken_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `egg_type` int(2) NOT NULL DEFAULT '0',
  `points_acquired` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_at` (`user_id`,`created_at`,`points_acquired`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_uid` int(11) NOT NULL,
  `game_point` int(11) NOT NULL,
  `game_date` varchar(15) NOT NULL,
  `game_time` varchar(30) NOT NULL,
  `game_score` int(11) DEFAULT '0',
  `game_type` int(11) DEFAULT '0',
  `mass_point` int(11) DEFAULT '0',
  `goal_point` int(11) DEFAULT '0',
  `ranking_point` int(11) DEFAULT '0',
  `attendance_point` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_seeker_daily`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_seeker_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT '-1',
  `clicked_day` date NOT NULL COMMENT 'YYYY-mm-dd',
  `token` varchar(32) NOT NULL COMMENT '每次请求重新生成',
  `token_updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `uid_daily` (`user_id`,`clicked_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='寻宝完成状态表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_seeker_points_pool`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_seeker_points_pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(8) NOT NULL COMMENT '每次发放的积分',
  `send_frequency` int(4) NOT NULL COMMENT '发放的频率',
  `is_published` tinyint(1) NOT NULL COMMENT '是否已经发布,1: wrote into cache file,   ',
  `published_at` datetime NOT NULL COMMENT '发布日期, auto publish',
  `is_valid` tinyint(1) NOT NULL COMMENT '是否生效, default 0',
  `updated_at` datetime NOT NULL COMMENT '更新日期, if has latest updated_at than cache ,do auto publish',
  `created_at` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`),
  KEY `pts_freq` (`points`,`send_frequency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='寻宝积分管理表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hobby_list`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hobby_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hobby_name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `identity_confirm`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `identity_confirm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `identity_card` varchar(50) DEFAULT NULL,
  `identity_validate_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `is_read_callboard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `is_read_callboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_cb_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jms_job_dependencies`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jms_job_dependencies` (
  `source_job_id` bigint(20) unsigned NOT NULL,
  `dest_job_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`source_job_id`,`dest_job_id`),
  KEY `IDX_8DCFE92CBD1F6B4F` (`source_job_id`),
  KEY `IDX_8DCFE92C32CF8D4C` (`dest_job_id`),
  CONSTRAINT `FK_8DCFE92C32CF8D4C` FOREIGN KEY (`dest_job_id`) REFERENCES `jms_jobs` (`id`),
  CONSTRAINT `FK_8DCFE92CBD1F6B4F` FOREIGN KEY (`source_job_id`) REFERENCES `jms_jobs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jms_job_related_entities`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jms_job_related_entities` (
  `job_id` bigint(20) unsigned NOT NULL,
  `related_class` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `related_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`job_id`,`related_class`,`related_id`),
  KEY `IDX_E956F4E2BE04EA9` (`job_id`),
  CONSTRAINT `FK_E956F4E2BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `jms_jobs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jms_job_statistics`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jms_job_statistics` (
  `job_id` bigint(20) unsigned NOT NULL,
  `characteristic` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` datetime NOT NULL,
  `charValue` double NOT NULL,
  PRIMARY KEY (`job_id`,`characteristic`,`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jms_jobs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jms_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `queue` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `priority` smallint(6) NOT NULL,
  `createdAt` datetime NOT NULL,
  `startedAt` datetime DEFAULT NULL,
  `checkedAt` datetime DEFAULT NULL,
  `executeAfter` datetime DEFAULT NULL,
  `closedAt` datetime DEFAULT NULL,
  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `args` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `output` longtext COLLATE utf8_unicode_ci,
  `errorOutput` longtext COLLATE utf8_unicode_ci,
  `exitCode` smallint(5) unsigned DEFAULT NULL,
  `maxRuntime` smallint(5) unsigned NOT NULL,
  `maxRetries` smallint(5) unsigned NOT NULL,
  `stackTrace` longblob COMMENT '(DC2Type:jms_job_safe_object)',
  `runtime` smallint(5) unsigned DEFAULT NULL,
  `memoryUsage` int(10) unsigned DEFAULT NULL,
  `memoryUsageReal` int(10) unsigned DEFAULT NULL,
  `originalJob_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_704ADB9349C447F1` (`originalJob_id`),
  KEY `cmd_search_index` (`command`),
  KEY `sorting_index` (`state`,`priority`,`id`),
  CONSTRAINT `FK_704ADB9349C447F1` FOREIGN KEY (`originalJob_id`) REFERENCES `jms_jobs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_daily_RR`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_daily_RR` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kpi_YMD` varchar(10) DEFAULT '',
  `register_YMD` varchar(10) DEFAULT '',
  `RR_day` int(3) NOT NULL DEFAULT '0',
  `register_user` int(11) NOT NULL DEFAULT '0',
  `active_user` int(11) NOT NULL DEFAULT '0',
  `RR` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpi_YMD` (`kpi_YMD`,`RR_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_summary`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_summary` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `kpi_month` varchar(11) DEFAULT NULL,
  `title` text,
  `summary` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='used by kpi_summary';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limit_ad`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limit_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_limit_ad_advertiserment1` (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `limit_ad_result`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `limit_ad_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adw_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `limit_ad_id` int(11) NOT NULL,
  `result_incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_date` datetime DEFAULT NULL,
  `login_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `market_activity`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `business_name` varchar(250) DEFAULT NULL,
  `category_id` varchar(250) DEFAULT NULL,
  `activity_url` varchar(1000) DEFAULT NULL,
  `activity_image` varchar(250) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  `activity_description` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `market_activity_click_number`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_activity_click_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `market_activity_id` int(11) NOT NULL,
  `click_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家活动点击数';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration_region_mapping`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_region_mapping` (
  `region_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region_id` (`region_id`,`province_id`,`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `month_income`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `month_income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `income` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer99_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer99_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer99_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offer99_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `tid` varchar(100) DEFAULT NULL,
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offerwow_api_return`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offerwow_api_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offerwow_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offerwow_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `happened_at` datetime DEFAULT NULL,
  `eventid` varchar(100) DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pag_order`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pag_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(50) NOT NULL,
  `point_uid` int(11) NOT NULL,
  `point_pid` varchar(50) NOT NULL,
  `date` varchar(20) NOT NULL,
  `date2` varchar(20) NOT NULL,
  `price` float NOT NULL,
  `status` int(11) NOT NULL,
  `amounts` float NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history00`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`),
  CONSTRAINT `fk_point_history_00_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history01`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history02`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history03`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history04`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history05`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history06`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history07`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history08`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_history09`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_history09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point_reason`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `points_exchange`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points_exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `exchange_date` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `type` int(11) NOT NULL,
  `target_account` varchar(45) DEFAULT NULL,
  `real_name` varchar(50) DEFAULT NULL,
  `source_point` int(11) NOT NULL,
  `target_point` int(11) NOT NULL,
  `exchange_item_number` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `points_exchange_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points_exchange_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provinceList`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provinceList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provinceName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qq_user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qq_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `open_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid_index` (`user_id`),
  KEY `openid_index` (`open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='qq用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rate_ad`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `income_rate` int(11) DEFAULT NULL,
  `incentive_rate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rate_ad_advertiserment1` (`ad_id`),
  CONSTRAINT `fk_rate_ad_advertiserment1` FOREIGN KEY (`ad_id`) REFERENCES `advertiserment` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rate_ad_result`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rate_ad_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adw_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rate_ad_id` int(11) NOT NULL,
  `result_price` int(11) NOT NULL,
  `result_incentive` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rate_ad_result_rate_ad1` (`rate_ad_id`),
  KEY `fk_rate_ad_result_user1` (`user_id`),
  CONSTRAINT `fk_rate_ad_result_rate_ad1` FOREIGN KEY (`rate_ad_id`) REFERENCES `rate_ad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_rate_ad_result_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `register_reward`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `register_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `rewards` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reward_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reward_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_callboard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_callboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT '0',
  `sendTo` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT '0',
  `delete_flag` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message00`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message01`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message02`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message03`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message04`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message05`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message06`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message07`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message08`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_message09`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sendFrom` int(11) DEFAULT NULL,
  `sendTo` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `createtime` datetime DEFAULT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `send_point_fail`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_point_fail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `send_type` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `set_password_code`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `set_password_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `is_available` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sop_profile_point`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sop_profile_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(16) DEFAULT NULL,
  `point_value` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(255) NOT NULL,
  `status_flag` tinyint(4) DEFAULT '1',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_uniq` (`hash`),
  KEY `panelist_status_idx` (`status_flag`,`user_id`),
  KEY `sop_status_idx` (`status_flag`,`id`),
  KEY `updated_at_idx` (`updated_at`),
  KEY `name_user_idx` (`name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sop_research_survey_participation_history`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sop_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_app_project_id` int(11) NOT NULL,
  `partner_app_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_app_member_uniq` (`partner_app_project_id`,`app_member_id`),
  KEY `project_updated_idx` (`partner_app_project_id`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sop_respondent`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sop_respondent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status_flag` tinyint(4) DEFAULT '1',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_uniq` (`user_id`),
  KEY `user_status_idx` (`status_flag`,`user_id`),
  KEY `sop_status_idx` (`status_flag`,`id`),
  KEY `updated_at_idx` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ssi_project`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ssi_project` (
  `id` int(10) unsigned NOT NULL,
  `status_flag` tinyint(1) unsigned DEFAULT '1' COMMENT '1: active,0:inactive',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPRESSED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ssi_project_respondent`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ssi_project_respondent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ssi_project_id` int(10) unsigned NOT NULL,
  `ssi_mail_batch_id` int(11) NOT NULL,
  `ssi_respondent_id` int(10) unsigned NOT NULL,
  `start_url_id` varchar(255) NOT NULL,
  `answer_status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0:init, 2:reopened, 5:forwarded ,11:completed',
  `stash_data` text,
  `completed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ssi_respondent_uniq` (`ssi_project_id`,`ssi_respondent_id`),
  KEY `ssi_project_respondent_uniq` (`ssi_project_id`,`ssi_respondent_id`),
  KEY `ssi_project_mail_batch_idx` (`ssi_project_id`,`ssi_mail_batch_id`),
  KEY `ssi_respondent_idx` (`ssi_respondent_id`),
  KEY `updated_at_answer_status_idx` (`updated_at`,`answer_status`),
  CONSTRAINT `fk_ssi_project_respondent_ssi_project1` FOREIGN KEY (`ssi_project_id`) REFERENCES `ssi_project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ssi_project_respondent_ssi_respondent1` FOREIGN KEY (`ssi_respondent_id`) REFERENCES `ssi_respondent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPRESSED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ssi_respondent`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ssi_respondent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `status_flag` smallint(3) unsigned DEFAULT '1' COMMENT '0:permission_no,1:permission_yes, 10:active',
  `stash_data` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partner_app_member_id_UNIQUE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taobao_category`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taobao_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(16) NOT NULL,
  `union_product` int(4) DEFAULT NULL,
  `delete_flag` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taobao_component`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taobao_component` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_id` int(11) NOT NULL COMMENT '1：搜索框 2：分类产品 3：单品 4：店铺',
  `category_id` int(11) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `sort` tinyint(3) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `component_index` (`component_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taobao_recommend`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taobao_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component_ids` varchar(255) NOT NULL,
  `recommend_name` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taobao_self_promotion_products`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taobao_self_promotion_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taobao_category_id` int(11) NOT NULL,
  `title` varchar(64) DEFAULT NULL,
  `price` float(9,2) NOT NULL DEFAULT '0.00',
  `price_promotion` float(9,2) NOT NULL DEFAULT '0.00',
  `item_url` varchar(255) DEFAULT NULL,
  `click_url` text NOT NULL,
  `picture_name` varchar(64) NOT NULL,
  `comment_description` varchar(255) DEFAULT '',
  `promotion_rate` float(9,2) NOT NULL DEFAULT '0.00',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `taobao_category_id` (`taobao_category_id`),
  CONSTRAINT `taobao_self_promotion_products_ibfk_1` FOREIGN KEY (`taobao_category_id`) REFERENCES `taobao_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history00`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history01`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history02`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history03`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history04`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history05`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history06`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history07`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history08`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_history09`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_history09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `task_type` int(11) NOT NULL,
  `category_type` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `reward_percent` float DEFAULT NULL,
  `point` int(11) DEFAULT NULL,
  `ocd_created_date` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) DEFAULT NULL,
  `pwd` varchar(45) DEFAULT NULL,
  `is_email_confirmed` int(11) DEFAULT NULL,
  `is_from_wenwen` int(1) DEFAULT NULL,
  `wenwen_user` varchar(100) DEFAULT NULL,
  `token` varchar(32) NOT NULL DEFAULT '' COMMENT 'remember me cookie token ',
  `nick` varchar(100) DEFAULT NULL,
  `sex` int(1) DEFAULT NULL,
  `birthday` varchar(50) DEFAULT NULL,
  `tel` varchar(45) DEFAULT NULL,
  `is_tel_confirmed` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `education` int(11) DEFAULT NULL COMMENT '学历',
  `profession` int(11) DEFAULT NULL COMMENT '职业',
  `income` int(11) DEFAULT NULL,
  `hobby` varchar(250) DEFAULT NULL COMMENT '爱好',
  `personalDes` text COMMENT '个性说明',
  `identity_num` varchar(40) DEFAULT NULL,
  `reward_multiple` float DEFAULT '1',
  `register_date` datetime DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_ip` varchar(20) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  `is_info_set` int(11) NOT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `uniqkey` varchar(250) DEFAULT NULL,
  `token_created_at` datetime DEFAULT NULL COMMENT 'remember me cookie token created at',
  `origin_flag` smallint(4) DEFAULT NULL COMMENT 'which sites does the user from',
  `created_remote_addr` varchar(20) DEFAULT NULL COMMENT 'remote IP when create',
  `created_user_agent` text COMMENT 'remote User Agent when create',
  `campaign_code` varchar(100) DEFAULT NULL COMMENT 'recruit campaign code',
  `password_choice` smallint(4) DEFAULT NULL COMMENT 'which password to use for login',
  `fav_music` varchar(255) DEFAULT NULL COMMENT '喜欢的音乐',
  `monthly_wish` varchar(255) DEFAULT NULL COMMENT '本月心愿',
  `industry_code` int(11) DEFAULT NULL COMMENT '行业',
  `work_section_code` int(11) DEFAULT NULL COMMENT '部门',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_91ww_visit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_91ww_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_advertiserment_visit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_advertiserment_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_configurations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_configurations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `flag_data` tinyint(1) DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_id1_flag_name1` (`user_id`,`flag_name`),
  KEY `IDX_6899B580A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_edm_unsubscribe`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_edm_unsubscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_game_visit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_game_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info_visit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_last`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_last` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT '0',
  `last_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_sign_up_route`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sign_up_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `source_route` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ind_user_sign_up_route_user_id1_source_route1` (`user_id`,`source_route`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_taobao_visit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_taobao_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_visit_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_visit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_flag` int(4) DEFAULT '-1' COMMENT '分类标志位',
  `user_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `created_at` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY `indx_user_target_daily` (`user_id`,`target_flag`,`visit_date`),
  KEY `indx_user_target` (`user_id`,`target_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_wenwen_cross`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_wenwen_cross` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_wenwen_cross_token`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_wenwen_cross_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cross_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cross_id` (`cross_id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_wenwen_login`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_wenwen_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_password_salt` text COMMENT 'The salt for encrypt password',
  `login_password_crypt_type` varchar(50) DEFAULT NULL COMMENT 'the encrypt method name',
  `login_password` text COMMENT 'the encrypted text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_wenwen_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `point_value` int(11) DEFAULT NULL,
  `stash_data` text,
  `vote_image` varchar(255) DEFAULT NULL,
  `delete_flag` tinyint(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote_answer`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vote_id` int(11) NOT NULL,
  `answer_number` tinyint(4) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`vote_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote_answer_result`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote_answer_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vote_id` int(11) NOT NULL,
  `answer_number` tinyint(4) NOT NULL,
  `answer_count` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vote_id` (`vote_id`,`answer_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weibo_user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weibo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'jili用户id',
  `open_id` varchar(255) DEFAULT NULL COMMENT '微博id唯一标识',
  `regist_date` datetime DEFAULT NULL COMMENT '注册日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id_UNIQUE` (`open_id`),
  KEY `userid_index` (`user_id`),
  KEY `openid_index` (`open_id`),
  KEY `fk_userid` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='weibo用户表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-04-06 15:40:20
