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