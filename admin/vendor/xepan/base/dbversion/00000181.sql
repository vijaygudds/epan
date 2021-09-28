ALTER TABLE `custom_form` ADD COLUMN `is_create_lead` tinyint(4) DEFAULT 0;
ALTER TABLE `custom_form` ADD COLUMN `is_associate_lead` tinyint(4) DEFAULT 0;
ALTER TABLE `custom_form` ADD COLUMN `lead_category_ids` text DEFAULT NULL;
ALTER TABLE `custom_form_field` ADD COLUMN `save_into_field_of_lead` varchar(255) DEFAULT NULL;