ALTER TABLE `task` ADD COLUMN `is_reminder_only`  tinyint(4) NULL DEFAULT NULL AFTER `notify_to`;
ALTER TABLE `task` DROP COLUMN `is_reminder`;