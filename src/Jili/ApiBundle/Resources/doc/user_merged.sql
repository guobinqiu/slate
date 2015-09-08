-- Mon Sep  7 15:07:28 CST 2015
-- Jarod
ALTER TABLE `user` ADD `origin_flag` TINYINT( 4 ) NOT NULL DEFAULT '0' AFTER `email` ;

ALTER TABLE `user` ADD `login_password` TEXT NULL ,
ADD `login_password_crypt_type` VARCHAR( 50 ) NULL ,
ADD `password_salt` TEXT NULL ;

ALTER TABLE `user` ADD `created_remote_addr` VARCHAR( 20 ) NULL ,
ADD `created_user_agent` VARCHAR( 100 ) NULL ,
ADD `campaign_code` VARCHAR( 100 ) NULL ;
~                                         
