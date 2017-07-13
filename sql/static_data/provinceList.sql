-- MySQL dump 10.13  Distrib 5.6.10, for Linux (x86_64)
--
-- Host: localhost    Database: jili_db
-- ------------------------------------------------------
-- Server version	5.6.10

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
-- Table structure for table `provinceList`
--

DROP TABLE IF EXISTS `provinceList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provinceList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provinceName` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provinceList`
--

LOCK TABLES `provinceList` WRITE;
/*!40000 ALTER TABLE `provinceList` DISABLE KEYS */;
INSERT INTO `provinceList` VALUES (1,'直辖市'),(2,'河北省'),(3,'山西省'),(4,'内蒙古自治区'),(5,'辽宁省'),(6,'吉林省'),(7,'黑龙江省'),(8,'江苏省'),(9,'浙江省'),(10,'安徽省'),(11,'福建省'),(12,'江西省'),(13,'山东省'),(14,'河南省'),(15,'湖北省'),(16,'湖南省'),(17,'广东省'),(18,'广西壮族自治区'),(19,'海南省'),(20,'四川省'),(21,'贵州省'),(22,'云南省'),(23,'西藏自治区'),(24,'陕西省'),(25,'甘肃省'),(26,'青海省'),(27,'宁夏回族自治区'),(28,'新疆维吾尔自治区'),(29,'香港特别行政区'),(30,'澳门特别行政区'),(31,'台湾省'),(32,'其他');
/*!40000 ALTER TABLE `provinceList` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-13 16:02:44
