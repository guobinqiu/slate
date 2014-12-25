

-- 修改table taobao_category
ALTER TABLE  `taobao_category` ADD  `union_product` INT( 4 ) NULL DEFAULT NULL AFTER  `category_name`;

-- 将之前的组件商品的联盟产品union_product设为2
UPDATE `taobao_category` SET `union_product` = 2 WHERE `union_product` IS NULL;

--
-- 转存表中的数据 `taobao_category`
--

INSERT INTO `taobao_category` 
( `category_name`,`union_product`,`delete_flag`, `created_at`, `updated_at`) VALUES
( '9块9专区',1,0,null,null),
( '时尚女装',1,0,null,null),
( '精品男装',1,0,null,null),
( '母婴儿童',1,0,null,null),
( '男鞋女鞋',1,0,null,null),
( '家居百货',1,0,null,null),
( '美食特产',1,0,null,null),
( '包包配饰',1,0,null,null),
( '美容护肤',1,0,null,null),
( '数码电器',1,0,null,null),
( '文化体育',1,0,null,null),
( '更多商品',1,0,null,null);

--
-- 表的结构 `taobao_self_promotion_products`
--
DROP TABLE IF EXISTS `taobao_self_promotion_products` ;
CREATE TABLE `taobao_self_promotion_products` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `taobao_category_id` int(11) NOT NULL,
      `title` varchar(64) DEFAULT NULL,
      `price` float(9,2) NOT NULL DEFAULT '0.00',
      `price_promotion` float(9,2) NOT NULL DEFAULT '0.00',
      `item_url` varchar(255) NOT NULL,
      `click_url` text NOT NULL,
      `picture_name` varchar(64) NOT NULL,
      `comment_description` varchar(255) NOT NULL DEFAULT '',
      `promotion_rate` float(9,2) NOT NULL DEFAULT '0.00',
      `updated_at` datetime DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `taobao_category_id` (`taobao_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

-- ALTER TABLE  `taobao_self_promotion_products` CHANGE  `picture_name`  `picture_name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
-- ALTER TABLE  `taobao_self_promotion_products` ADD  `taobao_category_id` INT NOT NULL AFTER  `id` ,ADD INDEX (  `taobao_category_id` ) ;

