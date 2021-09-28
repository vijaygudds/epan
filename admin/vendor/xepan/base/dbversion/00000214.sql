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
  `list_data_download_layout` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `list_category`
-- ----------------------------
DROP TABLE IF EXISTS `list_category`;
CREATE TABLE `list_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_category_id` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `display_sequence` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `description` text,
  `custom_link` varchar(255) DEFAULT NULL,
  `is_website_display` tinyint(1) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `slug_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_parent_category_id` (`parent_category_id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `list_data_set`
-- ----------------------------
DROP TABLE IF EXISTS `list_data_set`;
CREATE TABLE `list_data_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `list_data_set_condition`
-- ----------------------------
DROP TABLE IF EXISTS `list_data_set_condition`;
CREATE TABLE `list_data_set_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_data_set_id` int(11) DEFAULT NULL,
  `filter_effected_field_id` int(11) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_data_set_id` (`list_data_set_id`),
  KEY `fk_filter_effected_field_id` (`filter_effected_field_id`)
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
  `is_public` tinyint(1) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `list_filter`
-- ----------------------------
DROP TABLE IF EXISTS `list_filter`;
CREATE TABLE `list_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `layout` text,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `list_filter_fields`
-- ----------------------------
DROP TABLE IF EXISTS `list_filter_fields`;
CREATE TABLE `list_filter_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_filter_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `default_value` text,
  `status` varchar(255) DEFAULT NULL,
  `populate_values_from_field_id` int(11) DEFAULT NULL,
  `filter_effected_field_id` int(11) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_filter_id` (`list_filter_id`),
  KEY `fk_populate_values_from_field_id` (`populate_values_from_field_id`),
  KEY `fk_filter_effected_field_id` (`filter_effected_field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;


-- ----------------------------
-- Table structure for `listing_category_list_data_association`
-- ----------------------------
DROP TABLE IF EXISTS `listing_category_list_data_association`;
CREATE TABLE `listing_category_list_data_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `list_category_id` int(11) DEFAULT NULL,
  `list_data_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`),
  KEY `fk_list_category_id` (`list_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `listing_status_activity`
-- ----------------------------
DROP TABLE IF EXISTS `listing_status_activity`;
CREATE TABLE `listing_status_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `on_status` varchar(255) DEFAULT NULL,
  `email_subject` varchar(255) DEFAULT NULL,
  `email_body` text,
  `sms_content` text,
  `email_send_to_creator` tinyint(1) DEFAULT NULL,
  `email_send_to_list_data_fields` varchar(255) DEFAULT NULL,
  `email_send_to_custom_email_ids` text,
  `sms_send_to_creator` tinyint(1) DEFAULT NULL,
  `sms_send_to_list_data_fields` varchar(255) DEFAULT NULL,
  `sms_send_to_custom_phone_numbers` text,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `xepan_listing_contact_plan_association`
-- ----------------------------
DROP TABLE IF EXISTS `xepan_listing_contact_plan_association`;
CREATE TABLE `xepan_listing_contact_plan_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plan_id` (`plan_id`),
  KEY `fk_contact_id` (`contact_id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for `xepan_listing_list_data_form_layout`
-- ----------------------------
DROP TABLE IF EXISTS `xepan_listing_list_data_form_layout`;
CREATE TABLE `xepan_listing_list_data_form_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `layout` text,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for `xepan_listing_plan`
-- ----------------------------
DROP TABLE IF EXISTS `xepan_listing_plan`;
CREATE TABLE `xepan_listing_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `valid_for_days` varchar(255) DEFAULT NULL,
  `number_of_list_detail_allowed` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
