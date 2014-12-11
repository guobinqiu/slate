INSERT INTO `user` (`id`, `email`, `pwd`,  `nick`, `register_date`, `points`, `delete_flag`,`token`,`reward_multiple`,`is_info_set`)  
VALUES (1110,'jinzhang@voyagegroup.com.cn','testpwd','jin','2014-04-16 21:17:44','3',0, 'a',1,1),
(1111,'zspike1985@139.com','testpwd','jin11','2014-05-16 21:17:44','3',0 , 'a',1,1),
(1112,'zspike1985@139.com','testpwd','jin11','2014-05-16 21:17:44','0',0 , 'a',1,1),
(1113,'aabbcc@139.com','testpwd','jin22','2014-09-16 21:17:44','3',0 , 'a',1,1),
(1114,'aabbcc1111@139.com','testpwd','jin33','2014-10-16 21:17:44','3',0 , 'a',1,1),
(1115,'aabbcc2222@139.com','testpwd','jin33','2014-11-16 21:17:44','3',0 , 'a',1,1);

INSERT INTO `task_history00` 
VALUES (1111,0,1111,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-05-16 09:32:04',2),(1112,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',2);
INSERT INTO `task_history01` 
VALUES (1113,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1114,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',2);
INSERT INTO `task_history02` 
VALUES (1115,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1116,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',2);
INSERT INTO `task_history03` 
VALUES (1117,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1118,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',3);
INSERT INTO `task_history04` 
VALUES (1119,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1120,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',1);
INSERT INTO `task_history05` 
VALUES (1121,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1122,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',2)
INSERT INTO `task_history06` 
VALUES (1123,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1124,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',4);
INSERT INTO `task_history06` 
VALUES (1125,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1126,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',4);
INSERT INTO `task_history07` 
VALUES (1127,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',2),(1128,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',4);
INSERT INTO `task_history08` 
VALUES (1129,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1130,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',4);
INSERT INTO `task_history09` 
VALUES (1131,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-09-16 09:32:04',1),(1132,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,'2014-08-11 09:32:04',4);

INSERT INTO `point_history00` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1111,1110,3,16,'2014-03-04 00:01:36');
INSERT INTO `point_history01` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1112,1115,1,16,'2014-08-04 00:01:36');
INSERT INTO `point_history02` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1113,1113,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history03` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1114,1115,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history04` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1115,1113,1,16,'2014-10-04 00:01:36');
INSERT INTO `point_history05` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1116,1112,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history06` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1117,1115,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history07` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1118,1113,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history08` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1119,1112,1,16,'2014-09-04 00:01:36');
INSERT INTO `point_history09` (`id`, `user_id`, `point_change_num`, `reason`, `create_time`) VALUES (1110,1115,1,16,'2014-09-04 00:01:36');