CREATE TABLE `graphical_report` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT COMMENT='' CHECKSUM=0 DELAY_KEY_WRITE=0;

CREATE TABLE `graphical_report_widget` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`graphical_report_id` int(11) DEFAULT NULL,
	`name` varchar(255) DEFAULT NULL,
	`class_path` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT COMMENT='' CHECKSUM=0 DELAY_KEY_WRITE=0;

-- ALTER TABLE `projectcomment` ADD COLUMN `action` varchar(255) CHARACTER SET utf8 DEFAULT NULL after `employee_id`;