DROP TABLE IF EXISTS `carousellayer`;
CREATE TABLE `carousellayer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carousel_image_id` int(11) DEFAULT NULL,
  `layer_type` varchar(255) DEFAULT NULL,
  `image_id` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `horizontal_position` varchar(255) DEFAULT NULL,
  `vertical_position` varchar(255) DEFAULT NULL,
  `show_transition` varchar(255) DEFAULT NULL,
  `hide_transition` varchar(255) DEFAULT NULL,
  `show_delay` varchar(255) DEFAULT NULL,
  `show_offset` varchar(255) DEFAULT NULL,
  `hide_offset` varchar(255) DEFAULT NULL,
  `hide_delay` varchar(255) DEFAULT NULL,
  `show_duration` int(11) DEFAULT NULL,
  `hide_duration` int(11) DEFAULT NULL,
  `is_static` tinyint(1) DEFAULT NULL,
  `layer_class` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carousel_image_id` (`carousel_image_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
ALTER TABLE `carousellayer` MODIFY COLUMN `width`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `carousellayer` MODIFY COLUMN `height`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `carouselcategory` MODIFY COLUMN `height`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;