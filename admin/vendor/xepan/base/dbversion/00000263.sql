ALTER TABLE `employee` ADD COLUMN `track_geolocation` tinyint(4) DEFAULT 0;
ALTER TABLE `employee` ADD COLUMN `last_latitude` varchar(50) DEFAULT 0;
ALTER TABLE `employee` ADD COLUMN `last_longitude` varchar(50) DEFAULT 0;
ALTER TABLE `employee` ADD COLUMN `last_geolocation_update` datetime DEFAULT NULL;
