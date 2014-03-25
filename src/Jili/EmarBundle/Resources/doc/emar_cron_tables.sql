
-- 'web_id,web_name,web_catid,logo_url,web_url,information,begin_date,end_date,commission';
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `emar_websites` ADD `web_catid` INT( 11 ) NULL COMMENT '商家网站所属分类的分类id' AFTER `web_id` ,
ADD INDEX ( `web_catid` ); 
-- TABLE emar_products_croned
-- $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
-- $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');

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
      `short_intro` varchar(255) DEFAULT '' COMMENT '商品详情',
      PRIMARY KEY (`id`),
      UNIQUE KEY `pid` (`pid`),
      KEY `wid_catid` (`web_id`,`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- ( id, web_id, web_name, web_catid, logo_url, web_or_url , commission , is_deleted,deleted_at, is_called(是否被回调过, called_at 上次回调时间, clicked: 0，是否被点过, upated_at ,created_at ) 
DROP TABLE IF EXISTS `emar_websites`;
CREATE TABLE `emar_websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_catid` int(11) NOT NULL COMMENT '商家网站所属分类的分类id',
  `commission` varchar(128) DEFAULT '' COMMENT ' 推广佣金比例信息',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '是否已经弃用, 0:末弃用',
  `position` int(11) NOT NULL COMMENT '商家网站显示的顺序',
  `is_hidden` tinyint(1) DEFAULT '1' COMMENT '是否不显示, 1:不用做页面显示',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ( id, web_id, web_name, web_catid, logo_url, web_or_url , commission , is_deleted,deleted_at, is_called(是否被回调过, called_at 上次回调时间, clicked: 0，是否被点过, upated_at ,created_at ) 
CREATE TABLE `emar_websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
  `web_name` varchar(128) DEFAULT '' COMMENT ' 商家网站的中文名称',
  `web_catid` int(11) NOT NULL COMMENT '商家网站所属分类的分类id',
  `logo_url` varchar(255) DEFAULT '' COMMENT ' 网站LOGO图片的URL',
  `web_or_url` varchar(255) DEFAULT '' COMMENT '商家网站的计费URL ',
  `begin_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '',
  `end_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '',
  `commission` varchar(128) DEFAULT '' COMMENT ' 推广佣金比例信息',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '是否已经弃用, 0:末弃用',
  `deleted_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '弃用时间',
  `is_called` tinyint(1) DEFAULT '0' COMMENT '是否被回调过',
  `last_called_at` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '推广佣金比例信息',
  `clicked` int(11) NOT NULL DEFAULT '0' COMMENT '被点过计数',
  `updated_at` datetime DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
);


