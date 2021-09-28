ALTER TABLE `communication` ADD COLUMN `related_contact_id` Int ;
ALTER TABLE `communication` ADD INDEX (`related_contact_id`);
ALTER TABLE `communication` MODIFY COLUMN `description` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;