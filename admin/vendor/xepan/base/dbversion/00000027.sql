CREATE TABLE `employee_allowed_leave` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`employee_id`  int(11) NOT NULL ,
`leave_id`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;
CREATE TABLE `employee_attandance` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`leave_id`  int(11) NOT NULL ,
`employee_id`  int(11) NOT NULL ,
`date`  datetime NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `employee_transaction` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`employee_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`type`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`narration`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `employee_transaction_row` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`employee_transaction_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`narration`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`due`  decimal(12,0) NOT NULL ,
`paid`  decimal(12,0) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `leave` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`type`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`allowed`  decimal(14,0) NOT NULL ,
`deduction`  decimal(14,0) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `leave_template` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `official_holidays` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`date`  datetime NOT NULL ,
`narration`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `payment_and_deduction` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`salary_template_id`  int(11) NOT NULL ,
`employee_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`type`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`amount`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

CREATE TABLE `salary_template` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
`is_template`  tinyint(4) NOT NULL ,
`type`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Compact
;

ALTER TABLE `employee` ADD COLUMN `leave_template_id`  int(11) NOT NULL AFTER `remark`;
ALTER TABLE `employee` ADD COLUMN `salary_template_id`  int(11) NOT NULL AFTER `leave_template_id`;

ALTER TABLE `post` ADD COLUMN `leave_template_id`  int(11) NOT NULL AFTER `out_time`;
ALTER TABLE `post` ADD COLUMN `salary_template_id`  int(11) NOT NULL AFTER `leave_template_id`;

ALTER TABLE `leave` ADD COLUMN `leave_template_id`  int(11) NOT NULL AFTER `deduction`;