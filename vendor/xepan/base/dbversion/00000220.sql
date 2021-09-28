ALTER TABLE `item_serial` ADD `contact_id` int ;
CREATE INDEX contact_id ON item_serial (contact_id); 