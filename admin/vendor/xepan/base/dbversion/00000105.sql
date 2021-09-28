ALTER TABLE `store_transaction` ADD `department_id` INT(11) NULL AFTER `related_transaction_id`; 
ALTER TABLE `store_transaction_row` ADD `extra_info` LONGTEXT NULL AFTER `status`;