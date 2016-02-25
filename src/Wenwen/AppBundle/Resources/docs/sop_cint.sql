CREATE TABLE `cint_permission` (
  `user_id` int(11) NOT NULL,
  `permission_flag` tinyint(4) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cint_user_agreement_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `agreement_status` int(11) NOT NULL DEFAULT '0',
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_uniq_key` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cint_research_survey_participation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cint_project_id` int(11) NOT NULL,
  `cint_project_quota_id` int(11) NOT NULL,
  `app_member_id` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `stash_data` text,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cint_project_member_uniq` (`cint_project_id`,`app_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;