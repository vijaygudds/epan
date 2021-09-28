ALTER TABLE `supplier` ADD COLUMN `bank_name` varchar(255) DEFAULT NULL;
ALTER TABLE `supplier` ADD COLUMN `bank_ifsc_code` varchar(255) DEFAULT NULL;
ALTER TABLE `supplier` ADD COLUMN `account_no` varchar(255) DEFAULT NULL;
ALTER TABLE `supplier` ADD COLUMN `account_type` varchar(255) DEFAULT NULL;