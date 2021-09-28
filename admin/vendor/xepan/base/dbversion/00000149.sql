ALTER TABLE `deduction` DROP `created_at` ;
ALTER TABLE `deduction` CHANGE `created_by_id` `document_id` INT( 11 ) NULL DEFAULT NULL ;
ALTER TABLE `salary` ADD `is_reimbursement` TINYINT( 1 ) NULL DEFAULT NULL;
ALTER TABLE `salary` ADD `is_deduction` TINYINT( 1 ) NULL DEFAULT NULL;