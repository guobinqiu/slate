--  用于cron
DROP TABLE IF EXISTS `emar_websites_category_cron`;
CREATE TABLE `emar_websites_category_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的id',
  `category_id` int(11) NOT NULL COMMENT '商品分类',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '计数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='emar开放接口商品的商家与类型的对应关系,用于cron' ;

--  用于查询
DROP TABLE IF EXISTS `emar_websites_category`;
CREATE TABLE `emar_websites_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的id',
  `category_id` int(11) NOT NULL COMMENT '商品分类',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '计数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `web_id` (`web_id`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='emar开放接口商品的商家与类型的对应关系,用于查询';

-- 'web_id,web_name,web_catid,logo_url,web_url,information,begin_date,end_date,commission';
--- 用于查询
DROP TABLE IF EXISTS `emar_websites_croned`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用于查询';

--- 用于下载
DROP TABLE IF EXISTS `emar_websites_cron`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用于下载';

-- TABLE emar_products_croned
-- $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
-- $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');

-- 用于查询
DROP TABLE IF EXISTS `emar_products_croned`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='用于查询'

--用于下载
DROP TABLE IF EXISTS `emar_products_croned`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='用于下载';
-- ( id, web_id, web_name, web_catid, logo_url, web_or_url , commission , is_deleted,deleted_at, is_called(是否被回调过, called_at 上次回调时间, clicked: 0，是否被点过, upated_at ,created_at ) 

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
  KEY `web_catid` (`web_catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 140409 11:22:37    81 Query 
-- 140409 11:22:51    81 Query 
-- create table emar_websites_cron like emar_websites_croned;
-- create table emar_products_cron like emar_products_croned;


-- ALTER TABLE `emar_websites` ADD `is_hot` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '是否为热卖商家' AFTER `is_hidden` 
-- ALTER TABLE `emar_websites` ADD `hot_at` DATETIME NOT NULL  COMMENT '热卖商家 排序' AFTER `is_hot`

--  -- ( id, web_id, web_name, web_catid, logo_url, web_or_url , commission , is_deleted,deleted_at, is_called(是否被回调过, called_at 上次回调时间, clicked: 0，是否被点过, upated_at ,created_at ) 
--  CREATE TABLE `emar_websites` (
--    `id` int(11) NOT NULL AUTO_INCREMENT,
--    `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
--    `web_name` varchar(128) DEFAULT '' COMMENT ' 商家网站的中文名称',
--    `web_catid` int(11) NOT NULL COMMENT '商家网站所属分类的分类id',
--    `logo_url` varchar(255) DEFAULT '' COMMENT ' 网站LOGO图片的URL',
--    `web_or_url` varchar(255) DEFAULT '' COMMENT '商家网站的计费URL ',
--    `begin_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '',
--    `end_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '',
--    `commission` varchar(128) DEFAULT '' COMMENT ' 推广佣金比例信息',
--    `is_deleted` tinyint(1) DEFAULT '0' COMMENT '是否已经弃用, 0:末弃用',
--    `deleted_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '弃用时间',
--    `is_called` tinyint(1) DEFAULT '0' COMMENT '是否被回调过',
--    `last_called_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '推广佣金比例信息',
--    `clicked` int(11) NOT NULL DEFAULT '0' COMMENT '被点过计数',
--    `updated_at` datetime DEFAULT '0000-00-00 00:00:00',
--    `created_at` datetime DEFAULT '0000-00-00 00:00:00',
--    PRIMARY KEY (`id`),
--  );


