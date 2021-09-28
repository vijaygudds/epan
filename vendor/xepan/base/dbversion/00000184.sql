ALTER TABLE `attachment` ADD COLUMN `contact_id` int DEFAULT NULL;
ALTER TABLE `attachment` ADD COLUMN `title` varchar(255) DEFAULT NULL;
ALTER TABLE `attachment` ADD COLUMN `description` text DEFAULT NULL;