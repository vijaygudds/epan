ALTER TABLE `carouselcategory` Add COLUMN `layout`  VARCHAR(255);
ALTER TABLE `carouselcategory` Add COLUMN `autoplay`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `show_arrows`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `show_buttons`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `thumbnail_pointer`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `thumbnail_width`  int(11);
ALTER TABLE `carouselcategory` Add COLUMN `thumbnail_height`  int(11);
ALTER TABLE `carouselcategory` Add COLUMN `auto_slide_size`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `auto_height`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `full_screen`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `visible_size`  varchar(255);
ALTER TABLE `carouselcategory` Add COLUMN `shuffle`  tinyint(4);
ALTER TABLE `carouselcategory` Add COLUMN `force_size`  VARCHAR(255);
ALTER TABLE `carouselcategory` Add COLUMN `orientation`  VARCHAR(255);
ALTER TABLE `carouselcategory` Add COLUMN `thumbnails_position`  VARCHAR(255);
ALTER TABLE `carouselcategory` Add COLUMN `thumbnail_arrows`  tinyint(4);
ALTER TABLE 'carouselcategory'  Add  COLUMN `width` int(11);
ALTER TABLE 'carouselcategory'  Add  COLUMN `height` int(11);

ALTER TABLE 'carouselimage'  Add  COLUMN `slide_type` varchar(255);

DROP TABLE IF EXISTS `carousellayer`;
CREATE TABLE `carousellayer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carousel_image_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
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
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_carousel_image_id` (`carousel_image_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;