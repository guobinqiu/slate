INSERT INTO `vote` (`id`, `title`, `description`, `start_time`, `end_time`, `point_value`, `stash_data`, `vote_image`, `updated_at`, `created_at`) VALUES
(1, '【生活】英语九大前缀 你认识哪个？', '[该题由热心用户：<a href="http://www.91wenwen.net/user/209656"><font color="red">坚挺小龙</font></a> 提供，恭喜他获得了<font color="red">200积分</font>！]英语有常用户的前缀，下列九大常用前缀，你认识哪个？', '2015-10-12 00:00:00', '2015-10-17 23:59:59', 1, '{"choices":{"1":"共：com-(还有con/cor/col的版本), per-","2":"分：dis-（常指一分为多）,se-（常指一分为二）","3":"前：pre-（在…之前）,pro-（往…前）","4":"上：sur-（超，过）"}}', 'e628eb1c9f5785d0825b03ba9f7c7bec.jpg', '2015-10-17 17:53:03', '2015-10-18 17:53:03'),
(2, '【生活】下面哪件是你认为自己做过最有爱心的事？', '[该题由热心用户：<a href="http://www.91wenwen.net/user/209656"><font color="red">坚挺小龙</font></a> 提供，恭喜他获得了<font color="red">200积分</font>！]一个人或多或少都做过帮助别人有爱心的事，至今为止，选出你认为自己做过的最有爱心的事？', DATE_ADD( NOW( ) , INTERVAL 1 DAY ), DATE_ADD( NOW( ) , INTERVAL 2 DAY ), 1, '{"choices":{"1":"无偿献血","2":"帮助走丢的小孩","3":"做义教等志愿活动","4":"收留流浪的小动物","5":"给乞讨者一些零钱"}}', 'bd44f20a59f0e8d2b551defc957248fb.jpg', NOW( ), NOW( )),
(3, '【生活】你认为红楼梦中最吸引人的角色是？', '[该题由热心用户：<a href="http://www.91wenwen.net/user/209656"><font color="red">坚挺小龙</font></a> 提供，恭喜他获得了<font color="red">200积分</font>！]每个人读红楼都会有不同的感受，请选出最吸引你的那个人物。', DATE_SUB( NOW( ) , INTERVAL 1 DAY ), DATE_ADD( NOW( ) , INTERVAL 2 DAY ), 1, '{"choices":{"1":"晴雯","2":"王熙凤","3":"贾宝玉","4":"林黛玉","5":"薛宝钗"}}', 'bd44f20a59f0e8d2b551defc957248fb.jpg', NOW( ), NOW( ));


INSERT INTO `vote_answer` (`id`, `user_id`, `vote_id`, `answer_number`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 1, NOW( ), NOW( )),
(2, 2, 1, 1, NOW( ), NOW( )),
(3, 2, 2, 1, NOW( ), NOW( ));


INSERT INTO `user` (`id`, `email`, `pwd`, `is_email_confirmed`, `is_from_wenwen`, `wenwen_user`, `token`, `nick`, `sex`, `birthday`, `tel`, `is_tel_confirmed`, `province`, `city`, `education`, `profession`, `income`, `hobby`, `personalDes`, `identity_num`, `reward_multiple`, `register_date`, `last_login_date`, `last_login_ip`, `points`, `delete_flag`, `is_info_set`, `icon_path`, `uniqkey`, `token_created_at`, `origin_flag`, `created_remote_addr`, `created_user_agent`, `campaign_code`, `password_choice`) VALUES
(1, 'test@test.com.cn', '38a124223e4c09ed42b9a16b320a3dbbb29b4776', NULL, NULL, NULL, '', 'aaa', NULL, NULL, 'bbb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2014-05-09 09:33:06', '2014-05-29 16:13:26', '::1', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
