-- MySQL dump 10.13  Distrib 5.6.17, for osx10.7 (x86_64)
--
-- Host: 192.168.1.36    Database: mypool3
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
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `pwd`, `is_email_confirmed`, `is_from_wenwen`, `wenwen_user`, `token`, `nick`, `sex`, `birthday`, `tel`, `is_tel_confirmed`, `province`, `city`, `education`, `profession`, `income`, `hobby`, `personalDes`, `identity_num`, `reward_multiple`, `register_date`, `last_login_date`, `last_login_ip`, `points`, `delete_flag`, `is_info_set`, `icon_path`, `uniqkey`, `token_created_at`) VALUES (1173775,'xuchuxiong@163.com','dcff293b3b2fa3cdf64bf9e2869eef5fb057eb18',NULL,2,NULL,'37f7e332ab0ea200987f7ec7f612bbbd','xcx5816',1,'1978-11','',NULL,17,211,NULL,NULL,110,'1,2,3,4,5,6,7,8,12',NULL,NULL,1,'2014-06-09 14:57:29','2014-08-29 11:08:00','183.62.199.225',190,NULL,1,NULL,'d493a3b4f81f93e96c7b995094616801ed3df01e','2014-08-29 11:07:58'),(1209735,'kimyam@foxmail.com','34ea88d5fde1b5aaf7aab1bb4e8d9413edb1e2de',NULL,2,NULL,'','Sula',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2014-07-22 22:39:47','2014-07-22 22:39:47',NULL,5,NULL,0,NULL,'5066f0892ebfa570b654fd7b109015203d9069aa',NULL);
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

-- Dump completed on 2014-09-19 10:28:55
