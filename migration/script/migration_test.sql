select now();

DROP DATABASE `jili_mig_dev`;

--
-- 数据库: `jili_mig_dev`
--
CREATE DATABASE IF NOT EXISTS `jili_mig_dev` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `jili_mig_dev`;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_wenwen_login`
--

CREATE TABLE IF NOT EXISTS `user_wenwen_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_password_salt` text COMMENT 'The salt for encrypt password',
  `login_password_crypt_type` varchar(50) DEFAULT NULL COMMENT 'the encrypt method name',
  `login_password` text COMMENT 'the encrypted text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `point_value` int(11) DEFAULT NULL,
  `stash_data` text,
  `vote_image` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `vote_answer`
--

CREATE TABLE IF NOT EXISTS `vote_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vote_id` int(11) NOT NULL,
  `answer_number` tinyint(4) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`vote_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_user`
--

CREATE TABLE IF NOT EXISTS `weibo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'jili用户id',
  `open_id` varchar(255) DEFAULT NULL COMMENT '微博id唯一标识',
  `regist_date` datetime DEFAULT NULL COMMENT '注册日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id_UNIQUE` (`open_id`),
  KEY `userid_index` (`user_id`),
  KEY `openid_index` (`open_id`),
  KEY `fk_userid` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='weibo用户表' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `sop_respondent` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- 表的结构 `migration_region_mapping`
--

CREATE TABLE IF NOT EXISTS `migration_region_mapping` (
  `region_id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region_id` (`region_id`,`province_id`,`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `month_income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `income` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE `ad_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) DEFAULT NULL,
  `asp` varchar(64) DEFAULT NULL COMMENT '平台供应商',
  `display_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;


--
-- 限制导出的表
--

--
-- 限制表 `user_wenwen_login`
--
ALTER TABLE `user_wenwen_login`
  ADD CONSTRAINT `user_wenwen_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- 转存表中的数据 `month_income`
--

INSERT INTO `month_income` (`id`, `income`) VALUES
(1, '3000元以下'),
(2, '3000元-5000元'),
(3, '5000元-10000元'),
(4, '10000元以上'),
(100, '1000元以下'),
(101, '1000元-1999元'),
(102, '2000元-2999元'),
(103, '3000元-3999元'),
(104, '4000元-4999元'),
(105, '5000元-5999元'),
(106, '6000元-6999元'),
(107, '7000元-7999元'),
(108, '8000元-8999元'),
(109, '9000元-9999元'),
(110, '10000元-11999元'),
(111, '12000元-13999元'),
(112, '14000元-15999元'),
(113, '16000元-17999元'),
(114, '18000元-19999元'),
(115, '20000元-23999元'),
(116, '24000元-27999元'),
(117, '28000元-31999元'),
(118, '32000元-35999元'),
(119, '36000元以上');


--
-- 转存表中的数据 `migration_region_mapping`
--

INSERT INTO `migration_region_mapping` (`region_id`, `province_id`, `city_id`) VALUES
(2000, 1, 2),
(2001, 1, 2),
(2002, 1, 1),
(2003, 1, 1),
(2004, 1, 3),
(2005, 1, 3),
(2006, 2, 6),
(2007, 2, 7),
(2008, 2, 8),
(2009, 2, 9),
(2010, 2, 10),
(2011, 2, 11),
(2012, 2, 12),
(2013, 2, 13),
(2014, 2, 14),
(2015, 2, 15),
(2016, 2, 5),
(2017, 3, 16),
(2018, 3, 17),
(2019, 3, 18),
(2020, 3, 19),
(2021, 3, 20),
(2022, 3, 21),
(2023, 3, 22),
(2024, 3, 23),
(2025, 3, 24),
(2026, 3, 25),
(2027, 3, 26),
(2028, 4, 27),
(2029, 4, 29),
(2030, 4, 30),
(2031, 4, 31),
(2032, 4, 32),
(2033, 4, 33),
(2034, 4, 34),
(2035, 4, 364),
(2036, 4, 35),
(2037, 4, 36),
(2038, 4, 37),
(2039, 4, 38),
(2040, 5, 39),
(2041, 5, 40),
(2042, 5, 41),
(2043, 5, 42),
(2044, 5, 43),
(2045, 5, 44),
(2046, 5, 45),
(2047, 5, 46),
(2048, 5, 47),
(2049, 5, 48),
(2050, 5, 49),
(2051, 5, 50),
(2052, 5, 51),
(2053, 5, 52),
(2054, 6, 53),
(2055, 6, 54),
(2056, 6, 55),
(2057, 6, 56),
(2058, 6, 57),
(2059, 6, 58),
(2060, 6, 59),
(2061, 6, 60),
(2062, 6, 61),
(2063, 7, 62),
(2064, 7, 63),
(2065, 7, 64),
(2066, 7, 65),
(2067, 7, 66),
(2068, 7, 67),
(2069, 7, 68),
(2070, 7, 69),
(2071, 7, 70),
(2072, 7, 71),
(2073, 7, 72),
(2074, 7, 73),
(2075, 7, 74),
(2076, 8, 75),
(2077, 8, 76),
(2078, 8, 77),
(2079, 8, 78),
(2080, 8, 79),
(2081, 8, 80),
(2082, 8, 81),
(2083, 8, 82),
(2084, 8, 83),
(2085, 8, 84),
(2086, 8, 85),
(2087, 8, 86),
(2088, 8, 87),
(2089, 9, 88),
(2090, 9, 89),
(2091, 9, 90),
(2092, 9, 91),
(2093, 9, 92),
(2094, 9, 93),
(2095, 9, 94),
(2096, 9, 95),
(2097, 9, 96),
(2098, 9, 97),
(2099, 9, 98),
(2100, 10, 110),
(2101, 10, 111),
(2102, 10, 112),
(2103, 10, 113),
(2104, 10, 114),
(2105, 10, 115),
(2106, 10, 116),
(2107, 10, 117),
(2108, 10, 118),
(2109, 10, 119),
(2110, 10, 120),
(2111, 10, 121),
(2112, 10, 122),
(2113, 10, 123),
(2114, 10, 124),
(2115, 10, 125),
(2116, 10, 126),
(2117, 11, 127),
(2118, 11, 128),
(2119, 11, 129),
(2120, 11, 130),
(2121, 11, 131),
(2122, 11, 132),
(2123, 11, 133),
(2124, 11, 134),
(2125, 11, 135),
(2126, 12, 136),
(2127, 12, 137),
(2128, 12, 138),
(2129, 12, 139),
(2130, 12, 140),
(2131, 12, 141),
(2132, 12, 142),
(2133, 12, 143),
(2134, 12, 144),
(2135, 12, 145),
(2136, 12, 146),
(2137, 13, 147),
(2138, 13, 148),
(2139, 13, 149),
(2140, 13, 150),
(2141, 13, 151),
(2142, 13, 152),
(2143, 13, 153),
(2144, 13, 154),
(2145, 13, 155),
(2146, 13, 156),
(2147, 13, 157),
(2148, 13, 158),
(2149, 13, 159),
(2150, 13, 160),
(2151, 13, 161),
(2152, 13, 162),
(2153, 13, 163),
(2154, 14, 164),
(2155, 14, 165),
(2156, 14, 166),
(2157, 14, 167),
(2158, 14, 168),
(2159, 14, 169),
(2160, 14, 170),
(2161, 14, 171),
(2162, 14, 172),
(2163, 14, 173),
(2164, 14, 174),
(2165, 14, 175),
(2166, 14, 176),
(2167, 14, 177),
(2168, 14, 178),
(2169, 14, 179),
(2170, 14, 180),
(2171, 15, 181),
(2172, 15, 182),
(2173, 15, 183),
(2174, 15, 184),
(2175, 15, 185),
(2176, 15, 186),
(2177, 15, 187),
(2178, 15, 188),
(2179, 15, 189),
(2180, 15, 190),
(2181, 15, 191),
(2182, 15, 192),
(2183, 15, 193),
(2184, 15, 194),
(2185, 16, 195),
(2186, 16, 196),
(2187, 16, 197),
(2188, 16, 198),
(2189, 16, 199),
(2190, 16, 200),
(2191, 16, 201),
(2192, 16, 202),
(2193, 16, 203),
(2194, 16, 204),
(2195, 16, 205),
(2196, 16, 206),
(2197, 16, 207),
(2198, 16, 208),
(2199, 17, 209),
(2200, 17, 210),
(2201, 17, 211),
(2202, 17, 212),
(2203, 17, 213),
(2204, 17, 214),
(2205, 17, 215),
(2206, 17, 216),
(2207, 17, 217),
(2208, 17, 218),
(2209, 17, 219),
(2210, 17, 220),
(2211, 17, 221),
(2212, 17, 222),
(2213, 17, 223),
(2214, 17, 224),
(2215, 17, 225),
(2216, 17, 226),
(2217, 17, 227),
(2218, 17, 228),
(2219, 17, 229),
(2220, 18, 230),
(2221, 18, 231),
(2222, 18, 232),
(2223, 18, 233),
(2224, 18, 234),
(2225, 18, 235),
(2226, 18, 236),
(2227, 18, 237),
(2228, 18, 238),
(2229, 18, 239),
(2230, 18, 240),
(2231, 18, 241),
(2232, 18, 242),
(2233, 18, 243),
(2234, 19, 244),
(2235, 19, 245),
(2236, 19, 246),
(2237, 1, 4),
(2238, 1, 4),
(2239, 1, 4),
(2240, 20, 247),
(2241, 20, 248),
(2242, 20, 249),
(2243, 20, 250),
(2244, 20, 251),
(2245, 20, 252),
(2246, 20, 253),
(2247, 20, 254),
(2248, 20, 255),
(2249, 20, 256),
(2250, 20, 257),
(2251, 20, 258),
(2252, 20, 259),
(2253, 20, 260),
(2254, 20, 261),
(2255, 20, 262),
(2256, 20, 263),
(2257, 20, 264),
(2258, 20, 265),
(2259, 20, 266),
(2260, 20, 267),
(2261, 21, 268),
(2262, 21, 269),
(2263, 21, 270),
(2264, 21, 271),
(2265, 21, 272),
(2266, 21, 273),
(2267, 21, 274),
(2268, 21, 275),
(2269, 21, 276),
(2270, 22, 277),
(2271, 22, 278),
(2272, 22, 279),
(2273, 22, 280),
(2274, 22, 281),
(2275, 22, 282),
(2276, 22, 283),
(2277, 22, 284),
(2278, 22, 285),
(2279, 22, 286),
(2280, 22, 287),
(2281, 22, 288),
(2282, 22, 289),
(2283, 22, 290),
(2284, 22, 291),
(2285, 22, 292),
(2286, 23, 293),
(2287, 23, 294),
(2288, 23, 295),
(2289, 23, 296),
(2290, 23, 297),
(2291, 23, 298),
(2292, 23, 299),
(2293, 24, 300),
(2294, 24, 301),
(2295, 24, 302),
(2296, 24, 303),
(2297, 24, 304),
(2298, 24, 305),
(2299, 24, 306),
(2300, 24, 307),
(2301, 24, 308),
(2302, 24, 309),
(2303, 25, 310),
(2304, 25, 311),
(2305, 25, 312),
(2306, 25, 313),
(2307, 25, 314),
(2308, 25, 315),
(2309, 25, 316),
(2310, 25, 317),
(2311, 25, 318),
(2312, 25, 319),
(2313, 25, 320),
(2314, 25, 321),
(2315, 25, 322),
(2316, 25, 323),
(2317, 26, 324),
(2318, 26, 325),
(2319, 26, 326),
(2320, 26, 327),
(2321, 26, 328),
(2322, 26, 329),
(2323, 26, 330),
(2324, 26, 331),
(2325, 27, 332),
(2326, 27, 333),
(2327, 27, 334),
(2328, 27, 335),
(2329, 27, 336),
(2330, 28, 337),
(2331, 28, 338),
(2332, 28, 339),
(2333, 28, 340),
(2334, 28, 341),
(2335, 28, 342),
(2336, 28, 343),
(2337, 28, 344),
(2338, 28, 345),
(2339, 28, 346),
(2340, 28, 347),
(2341, 28, 348),
(2342, 28, 349),
(2343, 28, 350),
(2344, 28, 351),
(2345, 33, 365),
(2346, 34, 366),
(2347, 35, 367),
(2348, 35, 368),
(2349, 35, 369),
(2350, 35, 370),
(2351, 35, 371),
(2352, 35, 372),
(2353, 35, 373),
(2354, 35, 374),
(2355, 36, 375);


INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (1,'CPA',NULL,'广告体验');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (2,'CPS','chanet','购物返利');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (3,NULL,NULL,'游戏广告');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (4,NULL,NULL,'游戏积分码');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (5,NULL,NULL,'游戏通关');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (6,NULL,NULL,'游戏排名');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (7,NULL,NULL,'游戏全勤');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (8,NULL,NULL,'91问问积分兑换');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (9,NULL,NULL,'完善资料');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (10,NULL,NULL,'亚马逊礼品卡');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (11,NULL,NULL,'支付宝');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (12,NULL,NULL,'手机费');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (13,NULL,NULL,'91问问兑换米粒');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (14,NULL,NULL,'名片录力');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (15,NULL,NULL,'积分失效');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (16,NULL,NULL,'每天签到');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (17,'OfferWow',NULL,'体验广告');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (18,'Offer99',NULL,'体验广告');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (19,'cps','emar','购物返利');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (20,'cpa','emar','亿玛活动');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (21,'event',NULL,'活动送积分');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (22,'bangwoya','天芒云','体验广告');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (23,'cps','duomai','购物返利');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (24,NULL,NULL,'流量包');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (30,'game','91jili','游戏寻宝箱');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (31,'game','91jili','游戏砸金蛋');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (90,NULL,NULL,'手动返还积分');
INSERT INTO `ad_category` (`id`,`category_name`,`asp`,`display_name`) VALUES (91,'system',NULL,'米粒误发修改');


INSERT INTO `ad_category` (`id`, `category_name`, `asp`, `display_name`) VALUES
(94, 'web_merge', '91wenwen', '网站合并');

CREATE TABLE `point_history00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`),
  CONSTRAINT `fk_point_history_00_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `point_history09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `point_change_num` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_point_history_00_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


select "migrate_vote";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_vote.csv' 
INTO TABLE vote 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

select "migrate_user";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_user.csv' 
INTO TABLE user 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

select "migrate_user_wenwen_login";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_user_wenwen_login.csv' 
INTO TABLE user_wenwen_login 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

select "migrate_weibo_user";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_weibo_user.csv' 
INTO TABLE weibo_user 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

select "migrate_sop_respondent";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_sop_respondent.csv' 
INTO TABLE sop_respondent 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

select "migrate_vote_answer";

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/migrate_vote_answer.csv' 
INTO TABLE vote_answer 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';


LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history00.csv' 
INTO TABLE task_history00 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history01.csv' 
INTO TABLE task_history01 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history02.csv' 
INTO TABLE task_history02 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history03.csv' 
INTO TABLE task_history03 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history04.csv' 
INTO TABLE task_history04 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history05.csv' 
INTO TABLE task_history05 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history06.csv' 
INTO TABLE task_history06 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history07.csv' 
INTO TABLE task_history07 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history08.csv' 
INTO TABLE task_history08 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/task_history09.csv' 
INTO TABLE task_history09 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history00.csv' 
INTO TABLE point_history00 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history01.csv' 
INTO TABLE point_history01 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history02.csv' 
INTO TABLE point_history02 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history03.csv' 
INTO TABLE point_history03 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history04.csv' 
INTO TABLE point_history04 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history05.csv' 
INTO TABLE point_history05 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history06.csv' 
INTO TABLE point_history06 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history07.csv' 
INTO TABLE point_history07 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history08.csv' 
INTO TABLE point_history08 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/var/www/html/jili-zhang/migration/script/export/point_history09.csv' 
INTO TABLE point_history09 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';


select now();