/*
CREATE TABLE `panel_region` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `panel_country_id` int(11) NOT NULL,
   `name` varchar(255) DEFAULT NULL,
   `name_native` varchar(255) DEFAULT NULL,
   `name_en` varchar(255) DEFAULT NULL,
   `category_name` varchar(255) DEFAULT NULL,
   `category_name_en` varchar(255) DEFAULT NULL,
   `sort_key` int(11) DEFAULT NULL,
   `visible_flag` tinyint(4) DEFAULT NULL,
   `delete_flag` tinyint(4) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `FI__21` (`panel_country_id`),
   CONSTRAINT `Rel_21` FOREIGN KEY (`panel_country_id`) REFERENCES `panel_country` (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=3800 DEFAULT CHARSET=utf8


CREATE TABLE `provinceList` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `provinceName` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8

CREATE TABLE `cityList` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `cityName` varchar(50) NOT NULL,
   `provinceId` int(11) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8
*/

CREATE TABLE IF NOT EXISTS `migration_region_mapping` (
  `region_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region_id` (`region_id`,`province_id`,`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--todo: 是否要加上这三个字段：sort_key, visible_flag, delete_flag 
--根据数据显示，可以不要这三个字段

UPDATE cityList SET cityName = '恩施土家族苗族自治州' WHERE cityName = '恩施土家族苗族自治区';
UPDATE cityList SET cityName = '湘西土家族苗族自治州' WHERE cityName = '湘西土家族苗族自治区';
UPDATE cityList SET cityName = '延边朝鲜族自治州' WHERE cityName = '延边朝鲜族自治区';
UPDATE cityList SET cityName = '兴安盟' WHERE cityName = '兴安盟市';
UPDATE cityList SET cityName = '楚雄彝族自治州' WHERE cityName = '楚雄彝族自治区';
UPDATE cityList SET cityName = '怒江傈僳族自治州' WHERE cityName = '怒江傈僳族自治区';