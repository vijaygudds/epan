ALTER TABLE `currency` ADD COLUMN `prefix`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `fractional_part`;
ALTER TABLE `currency` ADD COLUMN `postfix`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `prefix`;