ALTER TABLE `task` ADD COLUMN `related_id`  int(11) NULL DEFAULT NULL;
ALTER TABLE `task` DROP COLUMN `related_contact_id`;