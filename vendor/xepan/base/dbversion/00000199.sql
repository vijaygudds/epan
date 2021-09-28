SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `epan_category_association`
-- ----------------------------
DROP TABLE IF EXISTS `epan_category_association`;
CREATE TABLE `epan_category_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `epan_id` int(11) NOT NULL,
  `epan_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of epan_category_association
-- ----------------------------
SET FOREIGN_KEY_CHECKS=1;