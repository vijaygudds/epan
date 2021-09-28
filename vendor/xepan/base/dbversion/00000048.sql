ALTER TABLE `opportunity` MODIFY COLUMN `duration`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `title`;
ALTER TABLE `opportunity` MODIFY COLUMN `description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `lead_id`;
ALTER TABLE `opportunity` MODIFY COLUMN `assign_to_id`  int(11) NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `opportunity` MODIFY COLUMN `discount_percentage`  int(11) NULL DEFAULT NULL AFTER `fund`;
ALTER TABLE `opportunity` MODIFY COLUMN `narration`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `closing_date`;