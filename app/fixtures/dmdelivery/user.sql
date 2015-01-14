INSERT INTO `user` (`id`, `email`, `pwd`,  `nick`, `register_date`, `points`, `delete_flag`,`token`,`reward_multiple`,`is_info_set`)  
VALUES (1110,'jili_test@voyagegroup.com.cn','testpwd','jintest',DATE_SUB(CURDATE(), INTERVAL 1 YEAR),'3',0, 'a',1,1),
(1111,'jili_test@voyagegroup.com.cn','testpwd','jintest',DATE_SUB(CURDATE(), INTERVAL 1 YEAR),'3',0 , 'a',1,1),
(1112,'jili_test@voyagegroup.com.cn','testpwd','jintest',DATE_SUB(CURDATE(), INTERVAL 1 YEAR),'0',0 , 'a',1,1),
(1113,'aabbcc@139.com','testpwd','jin22',DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'3',0 , 'a',1,1),
(1114,'aabbcc1111@139.com','testpwd','jin33',DATE_SUB(CURDATE(), INTERVAL 1 WEEK),'3',0 , 'a',1,1),
(1115,'aabbcc2222@139.com','testpwd','jin32',DATE_SUB(CURDATE(), INTERVAL 1 MONTH),'3',0 , 'a',1,1);

INSERT INTO `point_history00` VALUES (1,1110,3,16,DATE_SUB(CURDATE(), INTERVAL 1 YEAR));
INSERT INTO `point_history01` VALUES (2,1115,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history02` VALUES (3,1113,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history03` VALUES (4,1115,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history04` VALUES (5,1113,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history05` VALUES (6,1112,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history06` VALUES (7,1115,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history07` VALUES (8,1113,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history08` VALUES (9,1112,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));
INSERT INTO `point_history09` VALUES (10,1115,1,16,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));

INSERT INTO `send_point_fail` VALUES (1,1110,150,DATE_SUB(CURDATE(), INTERVAL 7 MONTH));
INSERT INTO `send_point_fail` VALUES (2,1111,180,DATE_SUB(CURDATE(), INTERVAL 4 MONTH));
INSERT INTO `send_point_fail` VALUES (3,1114,173,DATE_SUB(CURDATE(), INTERVAL 2 MONTH));
INSERT INTO `send_point_fail` VALUES (4,1115,173,DATE_SUB(CURDATE(), INTERVAL 1 MONTH));

INSERT INTO `task_history00` 
VALUES (1,0,1111,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 YEAR),2),(12,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2);
INSERT INTO `task_history01` 
VALUES (2,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(13,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2);
INSERT INTO `task_history02` 
VALUES (3,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(14,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2);
INSERT INTO `task_history03` 
VALUES (4,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(15,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),3);
INSERT INTO `task_history04` 
VALUES (5,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(16,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1);
INSERT INTO `task_history05` 
VALUES (6,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(17,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2);
INSERT INTO `task_history06` 
VALUES (7,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(18,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),4);
INSERT INTO `task_history06` 
VALUES (8,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(19,0,1113,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),4);
INSERT INTO `task_history07` 
VALUES (9,0,1115,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2),(20,0,1112,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),4);
INSERT INTO `task_history08` 
VALUES (10,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),2),(21,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),4);
INSERT INTO `task_history09` 
VALUES (11,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),1),(22,0,1114,4,16,'每天签到获取米粒',NULL,1,NULL,DATE_SUB(CURDATE(), INTERVAL 1 MONTH),4);
