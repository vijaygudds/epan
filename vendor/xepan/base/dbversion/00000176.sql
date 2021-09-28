ALTER TABLE `taxation` ADD `show_in_qsp` TINYINT(4) NULL AFTER `sub_tax`;
UPDATE `taxation` SET `show_in_qsp` = '1';