CREATE TABLE `salary_ledger_association` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`ledger_id`  int(11) NOT NULL ,
`salary_id`  int(11) NOT NULL, 
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;