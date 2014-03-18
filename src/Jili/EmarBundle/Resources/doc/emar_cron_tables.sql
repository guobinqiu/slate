-- ( id, web_id, web_name, web_catid, logo_url, web_or_url , commission , is_deleted,deleted_at, is_called(是否被回调过, called_at 上次回调时间, clicked: 0，是否被点过, upated_at ,created_at ) 
DROP TABLE IF EXISTS `emar_websites`;
CREATE TABLE `emar_websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `web_id` int(11) NOT NULL COMMENT '商家网站的站点ID',
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



  `title` varchar(45) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL COMMENT 'for emar callback',
  `created_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `decription` varchar(1000) DEFAULT NULL,
  `content` text,
  `imageurl` varchar(250) DEFAULT NULL,
  `icon_image` varchar(250) DEFAULT NULL,
  `list_image` varchar(250) DEFAULT NULL,
  `incentive_type` int(1) DEFAULT NULL,
  `incentive_rate` int(6) DEFAULT NULL,
  `reward_rate` float NOT NULL DEFAULT '1',
  `incentive` int(11) DEFAULT NULL,
  `info` text,
  `category` int(11) DEFAULT '0',
  `delete_flag` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)

