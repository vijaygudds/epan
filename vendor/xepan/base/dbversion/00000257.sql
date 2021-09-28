DROP TABLE IF EXISTS `branch`;
CREATE TABLE `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `document` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `contact` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `project` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `ledger` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `account_transaction` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `store_transaction` ADD COLUMN `branch_id` int(11) DEFAULT NULL;
ALTER TABLE `acl` ADD COLUMN `is_branch_restricted` tinyint(4) DEFAULT 0;