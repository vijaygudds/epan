ALTER TABLE `task` ADD COLUMN `rejected_at`  datetime NULL DEFAULT NULL;
ALTER TABLE `task` ADD COLUMN `received_at`  datetime NULL DEFAULT NULL;
ALTER TABLE `task` ADD COLUMN `submitted_at`  datetime NULL DEFAULT NULL;
ALTER TABLE `task` ADD COLUMN `reopened_at`  datetime NULL DEFAULT NULL;
ALTER TABLE `task` ADD COLUMN `completed_at`  datetime NULL DEFAULT NULL;