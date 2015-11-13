LOAD DATA INFILE '/data/91jili/merge/script/vote/migrate_vote.csv' 
INTO TABLE vote 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '"' LINES 
TERMINATED BY '\r\n';


ALTER TABLE  `user` ADD  `fav_music` VARCHAR( 255 ) NULL COMMENT  'ϲ��������',
ADD  `monthly_wish` VARCHAR( 255 ) NULL COMMENT  '������Ը',
ADD  `industry_code` INT NULL COMMENT  '��ҵ',
ADD  `work_section_code` INT NULL COMMENT  '����';


UPDATE  `month_income` SET  `income` =  '1000Ԫ-1999Ԫ' WHERE  `month_income`.`id` =101;
UPDATE  `month_income` SET  `income` =  '2000Ԫ-2999Ԫ' WHERE  `month_income`.`id` =102;
UPDATE  `month_income` SET  `income` =  '3000Ԫ-3999Ԫ' WHERE  `month_income`.`id` =103;
UPDATE  `month_income` SET  `income` =  '4000Ԫ-4999Ԫ' WHERE  `month_income`.`id` =104;
UPDATE  `month_income` SET  `income` =  '5000Ԫ-5999Ԫ' WHERE  `month_income`.`id` =105;
UPDATE  `month_income` SET  `income` =  '6000Ԫ-6999Ԫ' WHERE  `month_income`.`id` =106;
UPDATE  `month_income` SET  `income` =  '7000Ԫ-7999Ԫ' WHERE  `month_income`.`id` =107;
UPDATE  `month_income` SET  `income` =  '8000Ԫ-8999Ԫ' WHERE  `month_income`.`id` =108;
UPDATE  `month_income` SET  `income` =  '9000Ԫ-9999Ԫ' WHERE  `month_income`.`id` =109;
UPDATE  `month_income` SET  `income` =  '10000Ԫ-11999Ԫ' WHERE  `month_income`.`id` =110;


INSERT INTO  `month_income` (`id` ,`income`) VALUES 
('111',  '12000Ԫ-13999Ԫ');
('112',  '14000Ԫ-15999Ԫ'),
('113',  '16000Ԫ-17999Ԫ'),
('114',  '18000Ԫ-19999Ԫ'),
('115',  '20000Ԫ-23999Ԫ'),
('116',  '24000Ԫ-27999Ԫ'),
('117',  '28000Ԫ-31999Ԫ'),
('118',  '32000Ԫ-35999Ԫ'),
('119',  '36000Ԫ����');
