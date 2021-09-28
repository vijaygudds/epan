ALTER TABLE `graphical_report` ADD COLUMN `is_system`  tinyint(4) NULL DEFAULT NULL;
ALTER TABLE `graphical_report` ADD COLUMN `description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL;