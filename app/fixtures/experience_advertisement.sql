-- MySQL dump 10.13  Distrib 5.6.17, for osx10.7 (x86_64)
--
-- Host: 192.168.1.36    Database: jili_0903
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.12.04.1-log

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
-- Dumping data for table `experience_advertisement`
--

LOCK TABLES `experience_advertisement` WRITE;
/*!40000 ALTER TABLE `experience_advertisement` DISABLE KEYS */;
INSERT INTO `experience_advertisement` (`id`, `mission_hall`, `point`, `mission_img_url`, `mission_title`, `delete_flag`, `create_time`, `update_time`) VALUES (1,1,140,'http://www.offer-wow.com/image/offerwow/logo/1406516261064.jpg','海富贵金属投资',NULL,'2014-08-08 00:00:00',NULL),(2,2,375,'http://www.offer99.com/app//appimg/1407464716.jpg','[试玩] 仙侠道-5服-侠影仙踪',NULL,'2014-08-08 00:00:00',NULL),(3,2,960,'http://www.offer99.com/app//appimg/1406703018.jpg','[试玩] 独步天下-19服',NULL,'2014-08-11 15:52:49',NULL),(4,1,1738559,'http://www.offer-wow.com/image/offerwow/logo/1404805015274.jpg','富贵乐园大奖第三期',1,'2014-08-11 15:52:49',NULL);
/*!40000 ALTER TABLE `experience_advertisement` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-19 15:08:00
