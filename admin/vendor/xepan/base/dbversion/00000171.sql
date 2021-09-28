ALTER TABLE `item` ADD `is_package` INT(11) NULL;

/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : ds

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-06-07 19:12:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `commerce_package_item_association`
-- ----------------------------
DROP TABLE IF EXISTS `commerce_package_item_association`;
CREATE TABLE `commerce_package_item_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_item_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
