/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : epan

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-11-30 12:40:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `freelancer_cat_customer_asso`
-- ----------------------------
DROP TABLE IF EXISTS `freelancer_cat_customer_asso`;
CREATE TABLE `freelancer_cat_customer_asso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `freelancer_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of freelancer_cat_customer_asso
-- ----------------------------

-- ----------------------------
-- Table structure for `freelancer_category`
-- ----------------------------
DROP TABLE IF EXISTS `freelancer_category`;
CREATE TABLE `freelancer_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of freelancer_category
-- ----------------------------