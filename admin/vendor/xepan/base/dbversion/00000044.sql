ALTER TABLE `opportunity` ADD COLUMN `assign_to_id`  int(11) NOT NULL AFTER `id`;
ALTER TABLE `opportunity` ADD COLUMN `fund`  decimal(14,0) NOT NULL AFTER `assign_to_id`;
ALTER TABLE `opportunity` ADD COLUMN `discount_percentage`  int(11) NOT NULL AFTER `fund`;
ALTER TABLE `opportunity` ADD COLUMN `closing_date`  datetime NOT NULL AFTER `discount_percentage`;
ALTER TABLE `opportunity` ADD COLUMN `narration`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `closing_date`;
ALTER TABLE `opportunity` ADD COLUMN `previous_status`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `narration`;
ALTER TABLE `opportunity` ADD COLUMN `probability_percentage`  decimal(14,0) NOT NULL AFTER `previous_status`;