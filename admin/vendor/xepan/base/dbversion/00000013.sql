ALTER TABLE `communication` ADD INDEX `to_id` USING BTREE (`to_id`) comment '';
ALTER TABLE `communication` ADD INDEX `from_id` USING BTREE (`from_id`) comment '';