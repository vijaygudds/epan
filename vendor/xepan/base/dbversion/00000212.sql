SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `list`
-- ----------------------------
DROP TABLE IF EXISTS `list`;
CREATE TABLE `list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `list_data_status` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `list_category`
-- ----------------------------
DROP TABLE IF EXISTS `list_category`;
CREATE TABLE `list_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `display_sequence` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `description` text,
  `custom_link` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `is_website_display` tinyint(1) DEFAULT NULL,
  `slug_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `list_fields`
-- ----------------------------
DROP TABLE IF EXISTS `list_fields`;
CREATE TABLE `list_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `is_mandatory` tinyint(1) DEFAULT NULL,
  `is_moderate` tinyint(1) DEFAULT NULL,
  `is_changable` tinyint(1) DEFAULT NULL,
  `is_filterable` tinyint(1) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `xepan_listing_list_data_form_layout`
-- ----------------------------
DROP TABLE IF EXISTS `xepan_listing_list_data_form_layout`;
CREATE TABLE `xepan_listing_list_data_form_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `layout` text,
  `list_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;