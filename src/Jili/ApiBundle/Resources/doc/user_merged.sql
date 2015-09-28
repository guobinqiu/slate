-- Mon Sep  7 15:07:28 CST 2015
-- Jarod
ALTER TABLE `user` ADD `origin_flag` TINYINT( 4 ) NULL COMMENT 'which sites does the user from',
ADD `created_remote_addr` VARCHAR( 20 ) NULL COMMENT 'remote IP when create',
ADD `created_user_agent` TEXT  NULL COMMENT 'remote User Agent when create',
ADD `campaign_code` VARCHAR( 100 ) NULL COMMENT 'recruit campaign code';


-- ALTER TABLE `user` CHANGE `created_user_agent` `created_user_agent` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'remote User Agent when create';



CREATE TABLE `user_wenwen_login` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_password_salt` text COMMENT 'The salt for encrypt password',
  `login_password_crypt_type` varchar(50) DEFAULT NULL COMMENT 'the encrypt method name',
  `login_password` text COMMENT 'the encrypted text',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_wenwen_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 
