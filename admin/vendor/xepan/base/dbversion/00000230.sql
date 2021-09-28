ALTER TABLE `rules` ADD `name` varchar(255) default 0;
ALTER TABLE `rules` ADD `status` varchar(10) default 0;
ALTER TABLE `rules` ADD `rulegroup_id` int(11);
ALTER TABLE `rule-options` ADD `name` varchar(255);
ALTER TABLE `rule-options` ADD `description` Text;
ALTER TABLE `rule-options` ADD `score_per_qty` int(11) default 0;
ALTER TABLE `task` ADD `applied_rules` varchar(255);
ALTER TABLE `task` ADD `manage_points` tinyint(4) default 0;
ALTER TABLE `point_system` ADD `created_by_id` int(11) default 0;
ALTER TABLE `point_system` ADD `timesheet_id` int(11) default 0;
ALTER TABLE `point_system` ADD `qty` int(11) default 0;

CREATE TABLE `rule_group` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255),
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact;

RENAME TABLE `rule-options` TO `rule_options`;