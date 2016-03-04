CREATE TABLE IF NOT EXISTS `fulcrum_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fulcrum_project_id` int(11) NOT NULL,
  `fulcrum_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fulcrum_project_member_uniq` (`fulcrum_project_id`,`app_member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `fulcrum_user_agreement_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_member_id` varchar(255) NOT NULL,
  `agreement_status` int(11) NOT NULL DEFAULT '0',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_member_id_uniq_key` (`app_member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8

