-- before deploy, applie this to current jili_db
-- issue_469 
-- Tue Oct 14 14:58:26 CST 2014
ALTER TABLE  `advertiserment` ADD  `is_expired`  tinyint( 1) NULL DEFAULT 0  COMMENT 'imageurl response reports expired' AFTER  `imageurl` ;

ALTER TABLE  `checkin_adver_list` ADD  `operation_method`  int(11) NULL DEFAULT 0  COMMENT '3: manual, 5:auto, 0 or 15: all ' AFTER `inter_space` ;
            

