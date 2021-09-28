CREATE TABLE `account_transaction_attachment` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`account_transaction_id`  int(11) NULL DEFAULT NULL ,
`file_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);