/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : epan

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-09-26 15:08:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `employee_leave_allow`
-- ----------------------------
DROP TABLE IF EXISTS `employee_leave_allow`;
CREATE TABLE `employee_leave_allow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `leave_id` int(11) DEFAULT NULL,
  `is_yearly_carried_forward` tinyint(4) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_unit_carried_forward` tinyint(4) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `allow_over_quota` tinyint(4) DEFAULT NULL,
  `no_of_leave` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of employee_leave_allow
-- ----------------------------

-- ----------------------------
-- Table structure for `leave_template`
-- ----------------------------
DROP TABLE IF EXISTS `leave_template`;
CREATE TABLE `leave_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of leave_template
-- ----------------------------

-- ----------------------------
-- Table structure for `leave_template_detail`
-- ----------------------------
DROP TABLE IF EXISTS `leave_template_detail`;
CREATE TABLE `leave_template_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_template_id` int(11) DEFAULT NULL,
  `leave_id` int(11) DEFAULT NULL,
  `is_yearly_carried_forward` tinyint(4) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_unit_carried_forward` tinyint(4) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `allow_over_quota` tinyint(4) DEFAULT NULL,
  `no_of_leave` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of leave_template_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `leaves`
-- ----------------------------
DROP TABLE IF EXISTS `leaves`;
CREATE TABLE `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `is_yearly_carried_forward` tinyint(4) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_unit_carried_forward` tinyint(4) DEFAULT NULL,
  `no_of_leave` decimal(10,0) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `allow_over_quota` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of leaves
-- ----------------------------
ALTER TABLE `salary` ADD `unit` VARCHAR(255) NOT NULL AFTER `add_deducat`;