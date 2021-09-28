ALTER TABLE `communication` ADD INDEX `related_id` USING BTREE (`related_id`) comment '';
ALTER TABLE `emailsetting` CHANGE COLUMN `last_emailed_at` `last_emailed_at` datetime DEFAULT NULL after `emails_in_BCC`;
ALTER TABLE `point_system` ADD INDEX `contact_id` USING BTREE (`contact_id`) comment '';
ALTER TABLE `point_system` ADD INDEX `landing_campaign_id` USING BTREE (`landing_campaign_id`) comment '';
ALTER TABLE `point_system` ADD INDEX `landing_content_id` USING BTREE (`landing_content_id`) comment '';
ALTER TABLE `point_system` ADD INDEX `created_at` USING BTREE (`created_at`) comment '';
