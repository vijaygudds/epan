ALTER TABLE `item` ADD `is_renewable` TINYINT ;
ALTER TABLE `item` ADD `remind_to` VARCHAR( 255 ) ;
ALTER TABLE `item` ADD `renewable_value` INT( 11 ) ;
ALTER TABLE `item` ADD `renewable_unit` VARCHAR( 255 ) ;