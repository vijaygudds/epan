ALTER TABLE `emailsetting` ADD COLUMN `is_active`  tinyint(4) NULL DEFAULT NULL AFTER `email_password`;
ALTER TABLE `emailsetting` ADD COLUMN `email_threshold_per_month`  int(11) NULL DEFAULT NULL;