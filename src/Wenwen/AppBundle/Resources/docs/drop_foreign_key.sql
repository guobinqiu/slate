ALTER TABLE activity_gathering_taobao_order DROP FOREIGN KEY activity_gathering_taobao_order_ibfk_1;
ALTER TABLE ad_position DROP FOREIGN KEY fk_ad_position_advertiserment1;
ALTER TABLE adw_access_history DROP FOREIGN KEY fk_adw_access_record_advertiserment1;
ALTER TABLE emar_access_history DROP FOREIGN KEY fk_emar_access_record_advertiserment1;
ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92C32CF8D4C;
ALTER TABLE jms_job_dependencies DROP FOREIGN KEY FK_8DCFE92CBD1F6B4F;
ALTER TABLE jms_job_related_entities DROP FOREIGN KEY FK_E956F4E2BE04EA9;
ALTER TABLE jms_jobs DROP FOREIGN KEY FK_704ADB9349C447F1;
ALTER TABLE point_history00 DROP FOREIGN KEY fk_point_history_00_user;
ALTER TABLE rate_ad DROP FOREIGN KEY fk_rate_ad_advertiserment1;
ALTER TABLE rate_ad_result DROP FOREIGN KEY fk_rate_ad_result_rate_ad1;
ALTER TABLE rate_ad_result DROP FOREIGN KEY fk_rate_ad_result_user1;
ALTER TABLE ssi_project_respondent DROP FOREIGN KEY fk_ssi_project_respondent_ssi_project1;
ALTER TABLE ssi_project_respondent DROP FOREIGN KEY fk_ssi_project_respondent_ssi_respondent1;
ALTER TABLE taobao_self_promotion_products DROP FOREIGN KEY taobao_self_promotion_products_ibfk_1;
ALTER TABLE user_wenwen_login DROP FOREIGN KEY user_wenwen_login_ibfk_1;