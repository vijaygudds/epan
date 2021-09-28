SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `unsubscribe`;
CREATE TABLE `unsubscribe` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`reason`  text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
`created_at`  datetime NULL DEFAULT NULL ,
`contact_id`  int(11) NULL DEFAULT NULL ,
`document_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=1;