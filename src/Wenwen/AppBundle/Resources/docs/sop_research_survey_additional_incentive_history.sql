CREATE TABLE `sop_research_survey_additional_incentive_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `hash` varchar(255) NOT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_uniq` (`hash`),
  KEY `project_updated_idx` (`survey_id`,`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;