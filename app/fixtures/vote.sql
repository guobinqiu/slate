INSERT INTO `vote` (`id`, `title`, `description`, `yyyymm`, `start_time`, `end_time`, `point_value`, `delete_flag`, `updated_at`, `created_at`) VALUES
(1, '成家&立业 孰先孰后？', '古话说先成家，后立业，大家怎么看', '201508', '2015-08-27 00:00:00', '2015-08-31 23:59:59', 1, 0, '2015-08-12 17:07:24', '2015-08-12 17:07:24'),
(2, '喜欢你的人，偷偷拍了一张你的照片，并保存做了手机桌面。', '你会生气吗？', '201508', '2015-08-26 00:00:00', '2015-08-30 23:59:59', 1, 0, '2015-08-12 17:07:24', '2015-08-12 17:07:24');

INSERT INTO `vote_choice` (`id`, `vote_id`, `answer_number`, `name`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 'sdf', '2015-08-12 17:07:24', '2009-11-27 18:23:46'),
(2, 1, 2, 'dgf', '2015-08-12 17:07:24', '2009-11-27 18:23:46'),
(3, 1, 3, NULL, '2015-08-12 17:07:24', '2009-11-27 18:23:46'),
(4, 1, 4, NULL, '2015-08-12 17:07:24', '2009-11-27 18:23:46'),
(5, 2, 1, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46'),
(6, 2, 2, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46'),
(7, 2, 3, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46'),
(8, 2, 4, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46'),
(9, 2, 5, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46'),
(10, 2, 6, NULL, '2015-08-12 17:07:24', '2009-11-30 18:43:46');

INSERT INTO `vote_image` (`id`, `vote_id`, `filename`, `description`, `width`, `height`, `sq_path`, `sq_width`, `sq_height`, `s_path`, `s_width`, `s_height`, `m_path`, `m_width`, `m_height`, `delete_flag`, `updated_at`, `created_at`) VALUES
(1, 1, 'c4206a37d99689c00aa30e8f9f6dff402e989c27.jpg', NULL, 302, 450, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_sq.jpg', 60, 60, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_s.jpg', 60, 90, 'c/4/c4206a37d99689c00aa30e8f9f6dff402e989c27_m.jpg', 80, 120, NULL, '2010-09-16 11:57:52', '2010-09-16 11:57:52'),
(2, 2, '49e09fb9ad3c670a87940be0f286dbfe9b892ed5.jpg', NULL, 450, 296, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_sq.jpg', 60, 60, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_s.jpg', 90, 59, '4/9/49e09fb9ad3c670a87940be0f286dbfe9b892ed5_m.jpg', 120, 78, NULL, '2010-09-16 11:58:22', '2010-09-16 11:58:22');


INSERT INTO `vote_answer_201508` (`id`, `user_id`, `vote_id`, `answer_number`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 1, NULL, NULL),
(2, 2, 1, 1, NULL, NULL),
(3, 2, 2, 1, NULL, NULL);
