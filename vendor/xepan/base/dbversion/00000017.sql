ALTER TABLE `account_transaction` ADD COLUMN `related_transaction_id` int(11) DEFAULT NULL after `round_amount`;
ALTER TABLE `account_transaction` ADD COLUMN `transaction_template_id` int(11) DEFAULT NULL after `related_transaction_id`;
ALTER TABLE `account_transaction_row` ADD COLUMN `code` varchar(255) CHARACTER SET utf8 DEFAULT NULL after `remark`;