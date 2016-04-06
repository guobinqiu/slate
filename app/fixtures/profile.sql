INSERT INTO `user` (`id`, `is_from_wenwen`, `wenwen_user`, `token`, `nick`, `pwd`, `sex`, `birthday`, `email`, `is_email_confirmed`, `tel`, `is_tel_confirmed`, `province`, `city`, `education`, `profession`, `income`, `hobby`, `personalDes`, `identity_num`, `reward_multiple`, `register_date`, `last_login_date`, `last_login_ip`, `points`, `delete_flag`, `is_info_set`, `icon_path`, `uniqkey`, `token_created_at`, `origin_flag`, `created_remote_addr`, `created_user_agent`, `campaign_code`, `password_choice`, `fav_music`, `monthly_wish`, `industry_code`, `work_section_code`) VALUES
(1, NULL, NULL, '', 'sdf', '38a124223e4c09ed42b9a16b320a3dbbb29b4776', NULL, '1988-08-08', 'test@ec-navi.com.cn', NULL, '12345678901', NULL, 2, 6, 1, 1, 1, '1,2', 'personalDes', NULL, 1, '2014-05-09 09:33:06', '2015-11-06 14:17:37', '::1', 4000095, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 'fav_music', 'monthly_wish', 1, 1),
(2, 2, NULL, '', 'atg', 'sff', 2, '1941-2', 'test@voyagegroup.com.cn', NULL, '', NULL, 1, 1, NULL, NULL, 102, '1,2,3', NULL, NULL, 1, '2014-08-26 17:59:05', '2015-02-13 10:09:18', '::1', 831370, NULL, 1, NULL, '111', '2014-11-03 16:45:43', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(3, 2, 'xujf@voyagegroup.com.cn', '', 'aaaa', NULL, 2, '1941-2', 'test@d8aspring.com', 1, '18616399572', 1, 2, 7, 15, 1, 101, '1,2,3,4,6,7,8,9,10,11,12', '只有勇敢把握机会，你才会创造幸福。。。', '333333333333333333', 1, '2015-03-04 10:33:10', '2015-11-13 11:09:27', '::1', 11541, 1, 0, 'uploads/user/91/1377046582_2187.jpeg', '111', NULL, 1, '59.175.162.242', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36', '23123', 1, NULL, NULL, NULL, NULL);


INSERT INTO `user_wenwen_login` (`id`, `user_id`, `login_password_salt`, `login_password_crypt_type`, `login_password`) VALUES
(1, 2, 'd4a7121efe2cfca310cac1964971ce88', 'md5', '0cb1847156a96797a7934b9b8ae27ec8');

INSERT INTO `provinceList` (`id`, `provinceName`) VALUES
(1, '直辖市'),
(2, '河北省'),
(3, '山西省');

INSERT INTO `cityList` (`id`, `cityName`, `provinceId`) VALUES
(1, '上海市', 1),
(2, '北京市', 1),
(3, '天津市', 1),
(4, '重庆市', 1),
(5, '衡水市', 2),
(6, '石家庄市', 2),
(7, '唐山市', 2),
(8, '秦皇岛市', 2),
(9, '邯郸市', 2),
(10, '邢台市', 2),
(11, '保定市', 2),
(12, '张家口市', 2),
(13, '承德市', 2),
(14, '沧州市', 2),
(15, '廊坊市', 2),
(16, '太原市', 3),
(17, '大同市', 3),
(18, '阳泉市', 3),
(19, '长治市', 3),
(20, '晋城市', 3),
(21, '朔州市', 3),
(22, '晋中市', 3),
(23, '运城市', 3),
(24, '忻州市', 3),
(25, '临汾市', 3),
(26, '吕梁市', 3);

INSERT INTO `month_income` (`id`, `income`) VALUES
(1, '3000元以下'),
(2, '3000元-5000元'),
(3, '5000元-10000元'),
(4, '10000元以上'),
(100, '1000元以下'),
(101, '1000元-1999元'),
(102, '2000元-2999元');


INSERT INTO `hobby_list` (`id`, `hobby_name`) VALUES
(1, '上网'),
(2, '音乐'),
(3, '旅游'),
(4, '购物'),
(5, '运动'),
(6, '看书'),
(7, '游戏'),
(8, '娱乐'),
(9, '影视'),
(10, '动漫'),
(11, '时尚'),
(12, '艺术');