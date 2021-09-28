ALTER TABLE communication DROP INDEX IF EXISTS `search_string`;
ALTER TABLE communication ADD FULLTEXT INDEX `search_string` (`title`, `description`, `communication_type`,` from_raw`);