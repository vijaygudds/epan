ALTER TABLE `communication_sms_setting` ADD `created_by_id` VARCHAR(255) NOT NULL AFTER `sms_postfix`;
ALTER TABLE `emailsetting` ADD `created_by_id` VARCHAR(255) NOT NULL AFTER `epan_id`;