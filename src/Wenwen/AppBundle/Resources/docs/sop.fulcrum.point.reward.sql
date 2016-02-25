CREATE TABLE `fulcrum_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fulcrum_project_id` int(11) NOT NULL,
  `fulcrum_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `point` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `stash_data` longtext COLLATE utf8_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fulcrum_project_member_uniq` (`fulcrum_project_id`,`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE `fulcrum_user_agreement_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_member_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `agreement_status` int(11) NOT NULL,
  `stash_data` longtext COLLATE utf8_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_member_id_uniq_key` (`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
