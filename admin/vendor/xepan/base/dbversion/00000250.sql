CREATE TABLE `communication_related_employee` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`communication_id`  int(11) NULL DEFAULT NULL ,
`employee_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);