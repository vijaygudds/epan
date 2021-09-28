ALTER TABLE `custom_form` ADD COLUMN `created_at`  datetime NULL DEFAULT NULL AFTER `emailsetting_id`;
ALTER TABLE `custom_form` ADD COLUMN `created_by_id`  int(11) NULL DEFAULT NULL AFTER `created_at`;
ALTER TABLE `custom_form` ADD COLUMN `type`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `created_by_id`;
ALTER TABLE `custom_form` ADD COLUMN `status`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `type`;