-- /**********************************************************************************/
-- 增加多麦的ad_category.
INSERT INTO `ad_category` ( `id` , `category_name` , `asp` , `display_name` ) VALUES (23 , 'cps', 'duomai', '购物返利');
-- 设置 chanet的的ad_category.asp 
UPDATE ad_category SET asp ='chanet' WHERE id = 2 limit 1;

-- /**********************************************************************************/
-- 活动自定义链接列表
DROP TABLE IF EXISTS `duomai_advertisement`;
CREATE TABLE IF NOT EXISTS `duomai_advertisement` (
    `id` int(11) NOT NULL AUTO_INCREMENT,  
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `ads_name` varchar(64) NOT NULL COMMENT '活动名称',
    `ads_url` varchar(128) NOT NULL COMMENT '网址',
    `ads_commission` varchar(64) NOT NULL COMMENT '佣金',
    `start_time` date NOT NULL COMMENT '活动时间(起)',
    `end_time` date NOT NULL COMMENT '活动时间(止)',
    `category` varchar(128) NOT NULL COMMENT '活动分类',
    `return_day` int(2) NOT NULL DEFAULT 0 COMMENT '效果认定期RD', 
    `billing_cycle` varchar(255) NOT NULL COMMENT '结算周期',
    `link_custom` varchar(128) NOT NULL COMMENT '自定义链接',
    `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL DEFAULT 0 COMMENT '1: 使用中, 0: 不在使用' ,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

-- --活动分类 ads_cate--
-- 1 综合商城                                       -- 7 图书音像
-- 2 服装服饰                                       -- 8 鲜花礼品
-- 3 手机/数码/家电                                 -- 9 珠宝首饰
-- 4 美容化妆 家居家                                -- 10 食品/茶叶/酒水
-- 5 女性/内衣                                      -- 11 医药健康
-- 6 母婴/儿童用品                                  -- 12 成人保健

-- 13 汽车用品                                     -- 19 机票酒店旅游
-- 14 运动户外                                     -- 20 金融理财
-- 15 箱包/眼镜/鞋类                               -- 21 网络游戏
-- 16 团购                                         -- 22 娱乐交友
-- 17 电视购物                                     -- 23 网络服务/其他
-- 18 教育培训                                     -- 24 票务

-- 序号    商品名称    佣金比例    有效期  备注    
DROP TABLE IF EXISTS `duomai_commission`;
CREATE TABLE IF NOT EXISTS `duomai_commission` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL DEFAULT 0 COMMENT '1: 使用中, 0: 不在使用' ,
    `created_at` datetime NOT NULL  COMMENT '写入时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `duomai_commission_data`;
CREATE TABLE IF NOT EXISTS `duomai_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `commission` varchar(100) DEFAULT NULL COMMENT '佣金比例',
  `commission_period` varchar(100) DEFAULT NULL COMMENT '有效期',
  `description` text COMMENT '备注',
  `created_at` datetime NOT NULL  COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 记录用户购买的商品的详情
-- 回调时生成的订单表
DROP TABLE IF EXISTS `duomai_order`;
CREATE TABLE IF NOT EXISTS `duomai_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT ,
  `user_id` int(11) NOT NULL COMMENT 'euid网站主设定的反馈标签',
  `ocd` varchar(32) NOT NULL COMMENT '请求参数中的id',
  `ads_id` int(11) NOT NULL COMMENT '活动ID',
  `ads_name` varchar(128) NOT NULL COMMENT '活动名称',
  `site_id` int(11) NOT NULL COMMENT '网站ID',
  `link_id` int(11) NOT NULL COMMENT '活动链接ID',
  `order_sn` varchar(32) NOT NULL COMMENT 'order_sn 订单编号',
  `order_time` datetime NOT NULL DEFAULT 0 COMMENT '下单时间',
  `orders_price` float(10,2) NOT NULL DEFAULT 0.0 COMMENT '订单金额',
  `comm` float(10,2) NOT NULL DEFAULT 0.0  COMMENT 'siter_commission 订单佣金',
  `status` int(2) NOT NULL DEFAULT 0 COMMENT '订单状态  -1 无效 0 未确认 1 确认 2 结算',
  `deactivated_at` datetime NOT NULL DEFAULT 0 COMMENT 'status= -1 的时间',
  `confirmed_at` datetime NOT NULL  DEFAULT 0 COMMENT 'status= 1 的时间',
  `balanced_at` datetime NOT NULL DEFAULT 0 COMMENT 'status= 2 的时间',
  `created_at` datetime NOT NULL DEFAULT 0 COMMENT 'status= 0 的时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_idx`(`site_id`,`ocd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

-- 记录多麦回调的原始请求参数
DROP TABLE IF EXISTS `duomai_api_return`;
CREATE TABLE IF NOT EXISTS `duomai_api_return` (
      `id` int(11) NOT NULL AUTO_INCREMENT,  
      `created_at` datetime NOT NULL,
      `content` text NOT NULL, -- 请求参数
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- /**********************************************************************************/

--  活动ID	 活动名称	 活动分类	 佣金	 结算周期	 链接类型	 文字链内容	 图片链接地址	 目标网址类型	 是否允许修改目标地址	 反馈标签	 自定义链接	
DROP TABLE IF EXISTS `emar_advertisement`;
CREATE TABLE IF NOT EXISTS `emar_advertisement` (
    `id` int(11) NOT NULL AUTO_INCREMENT,  
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `ads_name` varchar(64) NOT NULL  COMMENT '活动名称',
    `category` varchar(128) NOT NULL DEFAULT '' COMMENT '活动分类',
    `commission` varchar(128) NOT NULL DEFAULT '' COMMENT '佣金',
    `commission_period` varchar(100) DEFAULT '' COMMENT '结算周期',
    `ads_url` varchar(128) NOT NULL COMMENT '首页地址',
    `can_customize_target` int(1) NOT NULL DEFAULT 1 COMMENT '是否允许修改目标地址',
    `feedback_tag` varchar(4) NOT NULL DEFAULT 'c' COMMENT '反馈标签',
    `marketing_url` text NOT NULL  COMMENT '自定义链接',
    `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL DEFAULT 0 COMMENT '1: 使用中, 0: 不在使用' ,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

DROP TABLE IF EXISTS `emar_commission`;
CREATE TABLE IF NOT EXISTS `emar_commission` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL DEFAULT 0 COMMENT '1: 使用中, 0: 不在使用' ,
    `created_at` datetime NOT NULL  COMMENT '写入时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- # 序号,佣金类目,佣金,佣金周期,适用商品,详细说明,
DROP TABLE IF EXISTS `emar_commission_data`;
CREATE TABLE IF NOT EXISTS `emar_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT '' COMMENT '佣金类目',
  `commission` varchar(100) DEFAULT '' COMMENT '佣金',
  `commission_period` varchar(100) DEFAULT '' COMMENT '佣金周期',
  `product_apply_to` varchar(100) DEFAULT ''  COMMENT '适用商品',
  `description` text COMMENT '详细说明',
  `created_at` datetime NOT NULL  COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- /**********************************************************************************/
-- Chanet Related
DROP TABLE IF EXISTS `chanet_advertisement`;
CREATE TABLE IF NOT EXISTS `chanet_advertisement` (
    `id` int(11) NOT NULL AUTO_INCREMENT,  
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `ads_name` varchar(64) NOT NULL COMMENT '活动名称',
    `category` varchar(128) NOT NULL COMMENT '活动分类',
    `ads_url_type` varchar(128) NOT NULL COMMENT '链接类型',
    `ads_url` varchar(128) NOT NULL COMMENT '首页地址',
    `marketing_url` text NOT NULL COMMENT '推广链接',
    `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL COMMENT '1: 使用中, 0: 不在使用' ,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 


DROP TABLE IF EXISTS `chanet_commission`;
CREATE TABLE IF NOT EXISTS `chanet_commission` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ads_id` int(11) NOT NULL COMMENT '活动ID',
    `fixed_hash` varchar(64) NOT NULL  COMMENT '更新时使用',
    `is_activated` int(2) NOT NULL COMMENT '1: 使用中, 0: 不在使用' ,
    `created_at` datetime NOT NULL  COMMENT '写入时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `fixed_hash`(`fixed_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `chanet_commission_data`;
CREATE TABLE IF NOT EXISTS `chanet_commission_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `commission_id` int(11) NOT NULL COMMENT 'FK',
  `commission_serial_number` int(3) NOT NULL COMMENT '佣金序号',
  `commission_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `commission` varchar(100) DEFAULT NULL COMMENT '佣金比例',
  `commission_period` varchar(100) DEFAULT NULL COMMENT '有效期',
  `description` text COMMENT '备注',
  `created_at` datetime NOT NULL  COMMENT '写入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- /**********************************************************************************/
-- 需要支持:
-- 1. 找到website logo
-- 2. feedback param   
-- 3. no same or similar url_destination
-- 4. 方便在线更新。
DROP TABLE IF EXISTS `cps_advertisement`;
CREATE TABLE IF NOT EXISTS `cps_advertisement` (
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
    `website_host` varchar(128) NOT NULL  COMMENT '活动地址(商家名)的域名，用于找logo',
    `selected_at` datetime DEFAULT NULL COMMENT '选入cps_adver时间',
    `is_activated` int(2) NOT NULL DEFAULT 0  COMMENT '1: 使用中, 0: 不在使用 , 2: 丢弃' ,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ad_id` (`ad_category_id`,`ad_id`,`is_activated`),
    UNIQUE KEY `website_host` (`website_host`,`is_activated`),
    KEY `website_name_dictionary_key` (`website_name_dictionary_key`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

-- DROP TABLE IF EXISTS `advertisement_website_category`;
-- CREATE TABLE IF NOT EXISTS `cps_advertisement_website_category` (
--   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
--   `cat_name` varchar(64) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

