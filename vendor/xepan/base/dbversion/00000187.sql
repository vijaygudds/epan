ALTER TABLE `user` ADD COLUMN `access_token_expiry`  datetime NULL DEFAULT NULL;
ALTER TABLE `user` ADD COLUMN `access_token` varchar(255) DEFAULT NULL;