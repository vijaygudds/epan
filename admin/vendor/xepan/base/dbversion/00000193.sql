ALTER TABLE `item` ADD `slug_url` VARCHAR(255) NULL ;
UPDATE item SET slug_url = LOWER(sku);