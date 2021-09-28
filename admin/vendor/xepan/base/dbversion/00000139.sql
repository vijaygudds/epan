ALTER TABLE `unit_group` ADD `created_by_id` INT(11) NULL AFTER `name`; 
ALTER TABLE `salary` DROP `created_by_id`;
ALTER TABLE `salary_template_details` DROP `created_by_id`;