-- before deploy, applie this to current jili_db
-- issue_469 
-- Tue Oct 14 14:58:26 CST 2014

ALTER TABLE  `advertiserment` ADD  `is_expired`  tinyint( 1) NULL DEFAULT 0  COMMENT 'imageurl response reports expired' AFTER  `imageurl` ;

ALTER TABLE  `checkin_adver_list` ADD  `operation_method`  int(11) NULL DEFAULT 0  COMMENT '3: manual, 5:auto, 0 or 15: all ' AFTER `inter_space` ;
            
-- Wed Oct 15 10:11:20 CST 2014
CREATE TABLE `user_configurations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `flag_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
      `flag_data` tinyint(1) DEFAULT NULL,
      `updated_at` datetime NOT NULL,
      `created_at` datetime NOT NULL,
      `user_id` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_user_id1_flag_name1` (`user_id`,`flag_name`),
      KEY `IDX_6899B580A76ED395` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

-- Tue Oct 21 16:57:26 CST 2014
-- update checkin checkin_adver_list 
update checkin_adver_list set operation_method = 3 where ad_id = 33;
