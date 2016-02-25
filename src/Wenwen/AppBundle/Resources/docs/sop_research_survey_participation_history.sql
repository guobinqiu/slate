CREATE TABLE `sop_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_app_project_id` int(11) NOT NULL,
  `partner_app_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_app_member_uniq` (`partner_app_project_id`,`app_member_id`),
  KEY `project_updated_idx` (`partner_app_project_id`,`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;