/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : 127.0.0.1:3306
Source Database       : epan

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-03-01 18:08:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `report_executor`
-- ----------------------------
DROP TABLE IF EXISTS `report_executor`;
CREATE TABLE `report_executor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` varchar(255) DEFAULT NULL,
  `post` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `widget` varchar(255) DEFAULT NULL,
  `starting_from_date` date DEFAULT NULL,
  `financial_month_start` varchar(255) DEFAULT NULL,
  `time_span` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `schedule_date` date DEFAULT NULL,
  `data_range` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of report_executor
-- ----------------------------