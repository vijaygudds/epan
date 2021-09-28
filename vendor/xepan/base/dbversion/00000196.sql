/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : xepan2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-10-22 11:57:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `domain_detail`
-- ----------------------------
DROP TABLE IF EXISTS `domain_details`;
CREATE TABLE `domain_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` int(11) NOT NULL,
  `park_for_epan_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_detail` text,
  `created_at` datetime NOT NULL,
  `last_renew_at` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of domain_detail
-- ----------------------------