/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : epan

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-02-22 19:30:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `employee_app_associations`
-- ----------------------------
DROP TABLE IF EXISTS `employee_app_associations`;
CREATE TABLE `employee_app_associations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `installed_app_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of employee_app_associations
-- ----------------------------
INSERT INTO `employee_app_associations` VALUES ('20', '90', '1', '0');

ALTER TABLE `employee` ADD `finacial_permit_limit` INT(11) NULL AFTER `out_time`;
ALTER TABLE `post` ADD `finacial_permit_limit` INT(11) NULL AFTER `permission_level`;