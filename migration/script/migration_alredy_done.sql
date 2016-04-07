 CREATE TABLE `sop_respondent` (
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


 ALTER TABLE  `user` ADD  `fav_music` VARCHAR( 255 ) NULL COMMENT  '喜欢的音乐',
 ADD  `monthly_wish` VARCHAR( 255 ) NULL COMMENT  '本月心愿',
 ADD  `industry_code` INT NULL COMMENT  '行业',
 ADD  `work_section_code` INT NULL COMMENT  '部门';

 ALTER TABLE  `user_wenwen_login` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT ;


 UPDATE  `month_income` SET  `income` =  '1000元-1999元' WHERE  `month_income`.`id` =101;
 UPDATE  `month_income` SET  `income` =  '2000元-2999元' WHERE  `month_income`.`id` =102;
 UPDATE  `month_income` SET  `income` =  '3000元-3999元' WHERE  `month_income`.`id` =103;
 UPDATE  `month_income` SET  `income` =  '4000元-4999元' WHERE  `month_income`.`id` =104;
 UPDATE  `month_income` SET  `income` =  '5000元-5999元' WHERE  `month_income`.`id` =105;
 UPDATE  `month_income` SET  `income` =  '6000元-6999元' WHERE  `month_income`.`id` =106;
 UPDATE  `month_income` SET  `income` =  '7000元-7999元' WHERE  `month_income`.`id` =107;
 UPDATE  `month_income` SET  `income` =  '8000元-8999元' WHERE  `month_income`.`id` =108;
 UPDATE  `month_income` SET  `income` =  '9000元-9999元' WHERE  `month_income`.`id` =109;
 UPDATE  `month_income` SET  `income` =  '10000元-11999元' WHERE  `month_income`.`id` =110;
 
 
 INSERT INTO  `month_income` (`id` ,`income`) VALUES 
 ('111',  '12000元-13999元');
 ('112',  '14000元-15999元'),
 ('113',  '16000元-17999元'),
 ('114',  '18000元-19999元'),
 ('115',  '20000元-23999元'),
 ('116',  '24000元-27999元'),
 ('117',  '28000元-31999元'),
 ('118',  '32000元-35999元'),
 ('119',  '36000元以上');


 INSERT INTO `ad_category` (`id`, `category_name`, `asp`, `display_name`) VALUES
 (94, 'web_merge', '91wenwen', '网站合并');
