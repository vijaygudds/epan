ALTER TABLE `communication_read_emails` ADD INDEX contact_communication USING BTREE (`contact_id`,`communication_id`) comment '';
ALTER TABLE `communication_read_emails` ADD INDEX is_read USING BTREE (`is_read`) comment '';