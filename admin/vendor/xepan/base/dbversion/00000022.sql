/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : epan

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-09-19 12:39:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `store_transaction_row_custom_field_value`
-- ----------------------------
DROP TABLE IF EXISTS `store_transaction_row_custom_field_value`;
CREATE TABLE `store_transaction_row_custom_field_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customfield_generic_id` int(11) NOT NULL,
  `customfield_value_id` int(11) NOT NULL,
  `store_transaction_id` int(11) NOT NULL,
  `custom_name` varchar(255) NOT NULL,
  `custom_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of store_transaction_row_custom_field_value
-- ----------------------------