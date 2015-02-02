--修改 user_id unique index
ALTER TABLE `jili_db`.`activity_gathering_taobao_order` DROP INDEX `user_id`, ADD UNIQUE `user_order` (`user_id`, `order_identity`)COMMENT ''
