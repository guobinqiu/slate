
-- Tue Mar 11 09:09:48 CST 2014
ALTER TABLE `jili_db`.`adw_order` ADD INDEX `ad_id` ( `ad_id` ) 

ALTER TABLE `jili_db`.`user` ADD INDEX `email_idx` ( `email` ) 

ALTER TABLE `jili_db`.`adw_order` ADD INDEX `user_id` ( `ad_id` , `user_id` ) 

ALTER TABLE `jili_db`.`advertiserment` ADD INDEX `incentive_type` ( `incentive_type` ) 


DROP TABLE IF EXISTS `kpi_daily_RR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_daily_RR` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kpi_YMD` varchar(10) DEFAULT '',
  `register_YMD` varchar(10) DEFAULT '',
  `RR_day` int(3) NOT NULL DEFAULT '0',
  `register_user` int(11) NOT NULL DEFAULT '0',
  `active_user` int(11) NOT NULL DEFAULT '0',
  `RR` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpi_YMD` (`kpi_YMD`,`RR_day`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;