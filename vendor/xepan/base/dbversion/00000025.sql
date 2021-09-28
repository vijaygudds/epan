ALTER TABLE `store_transaction_row_custom_field_value` CHANGE `customfield_value_id` `customfield_value_id` INT(11) NULL; 
ALTER TABLE `store_transaction_row_custom_field_value` CHANGE `customfield_generic_id` `customfield_generic_id` INT(11) NULL;
ALTER TABLE `store_transaction_row_custom_field_value` CHANGE `store_transaction_row_id` `store_transaction_row_id` INT(11) NULL;
ALTER TABLE `store_transaction_row_custom_field_value` CHANGE `custom_name` `custom_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `store_transaction_row_custom_field_value` CHANGE `custom_value` `custom_value` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;