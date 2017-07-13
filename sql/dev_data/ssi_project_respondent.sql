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
-- Table structure for table `ssi_project_respondent`
--

DROP TABLE IF EXISTS `ssi_project_respondent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ssi_project_respondent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ssi_project_id` int(11) NOT NULL,
  `ssi_respondent_id` int(11) NOT NULL,
  `ssi_mail_batch_id` int(11) DEFAULT NULL,
  `start_url_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `answer_status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0:init, 2:reopened, 5:forwarded ,11:completed',
  `stash_data` longtext COLLATE utf8_unicode_ci,
  `completed_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ssi_respondent_uniq` (`ssi_project_id`,`ssi_respondent_id`),
  KEY `IDX_DCEFA6E9EBD1F782` (`ssi_project_id`),
  KEY `IDX_DCEFA6E98C48C5DD` (`ssi_respondent_id`),
  KEY `ssi_project_mail_batch_idx` (`ssi_project_id`,`ssi_mail_batch_id`),
  KEY `updated_at_answer_status_idx` (`updated_at`,`answer_status`),
  CONSTRAINT `FK_DCEFA6E98C48C5DD` FOREIGN KEY (`ssi_respondent_id`) REFERENCES `ssi_respondent` (`id`),
  CONSTRAINT `FK_DCEFA6E9EBD1F782` FOREIGN KEY (`ssi_project_id`) REFERENCES `ssi_project` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ssi_project_respondent`
--

LOCK TABLES `ssi_project_respondent` WRITE;
/*!40000 ALTER TABLE `ssi_project_respondent` DISABLE KEYS */;
INSERT INTO `ssi_project_respondent` VALUES (1,1,1,1,'hoge',1,'{\"startUrlHead\":\"http:\\/\\/www.d8aspring.com\\/?dummy=ssi-survey&id=\"}',NULL,'2017-07-13 16:19:46','2017-07-13 16:19:46');
/*!40000 ALTER TABLE `ssi_project_respondent` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-13 16:25:17
