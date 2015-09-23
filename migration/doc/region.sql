CREATE TABLE IF NOT EXISTS `migration_region_mapping` (
  `region_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region_id` (`region_id`,`province_id`,`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE cityList SET cityName = '恩施土家族苗族自治州' WHERE cityName = '恩施土家族苗族自治区';
UPDATE cityList SET cityName = '湘西土家族苗族自治州' WHERE cityName = '湘西土家族苗族自治区';
UPDATE cityList SET cityName = '延边朝鲜族自治州' WHERE cityName = '延边朝鲜族自治区';
UPDATE cityList SET cityName = '兴安盟' WHERE cityName = '兴安盟市';
UPDATE cityList SET cityName = '楚雄彝族自治州' WHERE cityName = '楚雄彝族自治区';
UPDATE cityList SET cityName = '怒江傈僳族自治州' WHERE cityName = '怒江傈僳族自治区';