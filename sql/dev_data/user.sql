-- MySQL dump 10.13  Distrib 5.6.36, for Linux (x86_64)
--
-- Host: localhost    Database: jili_dev
-- ------------------------------------------------------
-- Server version	5.6.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_email_confirmed` int(11) DEFAULT NULL,
  `nick` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_tel_confirmed` int(11) DEFAULT NULL,
  `reward_multiple` double NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `register_complete_date` datetime DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `points` int(11) NOT NULL,
  `delete_flag` int(11) DEFAULT NULL,
  `delete_date` datetime DEFAULT NULL,
  `icon_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_remote_addr` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'remote IP when create',
  `created_user_agent` longtext COLLATE utf8_unicode_ci COMMENT 'remote User Agent when create',
  `password_choice` smallint(6) DEFAULT NULL COMMENT 'which password to use for login',
  `last_get_points_at` datetime DEFAULT NULL COMMENT '最后一次获得(+)积分的时间',
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmation_token_expired_at` datetime DEFAULT NULL,
  `reset_password_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_token_expired_at` datetime DEFAULT NULL,
  `remember_me_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_me_token_expired_at` datetime DEFAULT NULL,
  `invite_id` int(11) DEFAULT NULL,
  `points_cost` int(11) NOT NULL DEFAULT '0',
  `points_expense` int(11) NOT NULL DEFAULT '0',
  `complete_n` int(11) NOT NULL DEFAULT '0',
  `screenout_n` int(11) NOT NULL DEFAULT '0',
  `quotafull_n` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  KEY `confirmation_token_idx` (`confirmation_token`),
  KEY `reset_password_token_idx` (`reset_password_token`),
  KEY `remember_me_token_idx` (`remember_me_token`),
  KEY `invite_id_idx` (`invite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/* email: test@d8aspring.com password: password */;
INSERT INTO `user` VALUES (1,'test@d8aspring.com','m1TD075CP2o=',1,'test@d8aspring.com',NULL,NULL,1,'2017-07-13 16:19:46','2017-07-13 16:19:46',NULL,NULL,NULL,100,NULL,NULL,'test/test_icon.jpg',NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-13 16:20:49
