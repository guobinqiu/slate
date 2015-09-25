INSERT INTO `vote` (`id`, `title`, `description`, `yyyymm`, `start_time`, `end_time`, `point_value`, `delete_flag`, `updated_at`, `created_at`) VALUES
(1, '成家&立业 孰先孰后？', '古话说先成家，后立业，大家怎么看', '201508', '2015-08-12 00:00:00', '2015-08-15 23:59:59', 1, 0, '2015-08-12 17:07:24', '2015-08-12 17:07:24'),
(2, '喜欢你的人，偷偷拍了一张你的照片，并保存做了手机桌面。', '你会生气吗？', EXTRACT( YEAR_MONTH FROM NOW( ) ), DATE_ADD( NOW( ) , INTERVAL 1 DAY ), DATE_ADD( NOW( ) , INTERVAL 2 DAY ), 1, 0, NOW( ), NOW( ));

INSERT INTO `vote_choice` (`id`, `vote_id`, `answer_number`, `name`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 'sdf', NOW( ), NOW( )),
(2, 1, 2, 'dgf', NOW( ), NOW( )),
(3, 1, 3, NULL, NOW( ), NOW( )),
(4, 1, 4, NULL, NOW( ), NOW( )),
(5, 2, 1, NULL, NOW( ), NOW( )),
(6, 2, 2, NULL, NOW( ), NOW( )),
(7, 2, 3, NULL, NOW( ), NOW( )),
(8, 2, 4, NULL, NOW( ), NOW( )),
(9, 2, 5, NULL, NOW( ), NOW( )),
(10, 2, 6, NULL, NOW( ), NOW( ));

INSERT INTO `vote_image` (`id`, `vote_id`, `filename`, `description`, `width`, `height`, `sq_path`, `sq_width`, `sq_height`, `s_path`, `s_width`, `s_height`, `m_path`, `m_width`, `m_height`, `delete_flag`, `updated_at`, `created_at`) VALUES
(1, 1, 'c4206a37d99689c00aa30e8f9f6dff402e989c27.jpg', NULL, 302, 450, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_sq.jpg', 60, 60, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_s.jpg', 60, 90, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_m.jpg', 80, 120, NULL, '2010-09-16 11:57:52', '2010-09-16 11:57:52'),
(2, 2, '49e09fb9ad3c670a87940be0f286dbfe9b892ed5.jpg', NULL, 450, 296, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_sq.jpg', 60, 60, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_s.jpg', 90, 59, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_m.jpg', 120, 78, NULL, NOW( ), NOW( ));

 CREATE TABLE IF NOT EXISTS vote_answer_201508 (
    id int(11) NOT NULL auto_increment,
    user_id int(11) NOT NULL,
    vote_id int(11) NOT NULL,
    answer_number tinyint(4) NOT NULL,
    updated_at datetime default NULL,
    created_at datetime default NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY (user_id,vote_id),
    KEY  (vote_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `vote_answer_201508` (`id`, `user_id`, `vote_id`, `answer_number`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 1, NOW( ), NOW( )),
(2, 2, 1, 1, NOW( ), NOW( )),
(3, 2, 2, 1, NOW( ), NOW( ));
