-- must backup the user & weibo_user
-- this script will load the merge result csv files into jili tables

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

truncate user;
truncate weibo_user;

CREATE TABLE `ssi_respondent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status_flag` tinyint(4) DEFAULT '1',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `panelist_uniq` (`panelist_id`),
  KEY `panelist_status_idx` (`status_flag`,`panelist_id`),
  KEY `ssi_status_idx` (`status_flag`,`id`),
  KEY `updated_at_idx` (`updated_at`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

LOAD DATA INFILE '/data/91jili/merge/export/migrate_vote.csv' 
INTO TABLE vote 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/migrate_user.csv' 
INTO TABLE user 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA INFILE '/data/91jili/merge/export/migrate_user_wenwen_login.csv' 
INTO TABLE user_wenwen_login 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n' 
IGNORE 1 LINES;

LOAD DATA INFILE '/data/91jili/merge/export/migrate_weibo_user.csv' 
INTO TABLE weibo_user 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/migrate_sop_respondent.csv' 
INTO TABLE sop_respondent 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/migrate_ssi_respondent.csv' 
INTO TABLE ssi_respondent 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/migrate_vote_answer.csv' 
INTO TABLE vote_answer 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history00.csv' 
INTO TABLE task_history00 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history01.csv' 
INTO TABLE task_history01 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history02.csv' 
INTO TABLE task_history02 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history03.csv' 
INTO TABLE task_history03 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history04.csv' 
INTO TABLE task_history04 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history05.csv' 
INTO TABLE task_history05 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history06.csv' 
INTO TABLE task_history06 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history07.csv' 
INTO TABLE task_history07 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history08.csv' 
INTO TABLE task_history08 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/task_history09.csv' 
INTO TABLE task_history09 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history00.csv' 
INTO TABLE point_history00 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history01.csv' 
INTO TABLE point_history01 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history02.csv' 
INTO TABLE point_history02 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history03.csv' 
INTO TABLE point_history03 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history04.csv' 
INTO TABLE point_history04 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history05.csv' 
INTO TABLE point_history05 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history06.csv' 
INTO TABLE point_history06 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history07.csv' 
INTO TABLE point_history07 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history08.csv' 
INTO TABLE point_history08 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

LOAD DATA INFILE '/data/91jili/merge/export/point_history09.csv' 
INTO TABLE point_history09 
CHARACTER SET UTF8  
FIELDS  TERMINATED BY ','  
OPTIONALLY ENCLOSED BY '"' 
ESCAPED BY '\\' LINES 
TERMINATED BY '\n';

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
