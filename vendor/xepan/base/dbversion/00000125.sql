ALTER TABLE `contact` ADD `related_with` VARCHAR(255) NULL AFTER `score`, ADD `related_id` INT(11) NULL AFTER `related_with`;