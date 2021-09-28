ALTER TABLE `webpage` Add COLUMN `is_secure` tinyint(4) DEFAULT 0;
ALTER TABLE `webpage` Add COLUMN `secure_only_for` varchar(255) DEFAULT NULL;