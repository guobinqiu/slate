CREATE TABLE `vote` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) DEFAULT NULL,
   `description` text,
   `start_time` datetime NOT NULL,
   `end_time` datetime NOT NULL,
   `point_value` int(11) DEFAULT NULL,
   `stash_data` text,
   `vote_image` varchar(255) DEFAULT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `vote_answer` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) NOT NULL,
   `vote_id` int(11) NOT NULL,
   `answer_number` tinyint(4) NOT NULL,
   `updated_at` datetime DEFAULT NULL,
   `created_at` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
   UNIQUE KEY `user_id` (`user_id`,`vote_id`),
   KEY `vote_id` (`vote_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `ad_category` (`id`, `category_name`, `asp`, `display_name`) VALUES
(92, 'questionnaire_cost', '91wenwen', '问卷回答'),
(93, 'questionnaire_expense', '91wenwen', '快速问答'),
(94, 'web_merge', '91wenwen', '网站合并');