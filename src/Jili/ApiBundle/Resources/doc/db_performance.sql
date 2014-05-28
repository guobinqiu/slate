
-- Tue Mar 11 09:09:48 CST 2014
ALTER TABLE `jili_db`.`adw_order` ADD INDEX `ad_id` ( `ad_id` ) 

ALTER TABLE `jili_db`.`user` ADD INDEX `email_idx` ( `email` ) 

ALTER TABLE `jili_db`.`adw_order` ADD INDEX `user_id` ( `ad_id` , `user_id` ) 

ALTER TABLE `jili_db`.`advertiserment` ADD INDEX `incentive_type` ( `incentive_type` ) 

