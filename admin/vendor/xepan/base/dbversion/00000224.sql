CREATE TABLE `xepan_auditlog` (
	`id` int NOT NULL AUTO_INCREMENT,
	`user_id` int,
	`contact_id` int,
	`model_class` varchar(255),
	`pk_id` int,
	`created_at` datetime,
	`name` text,
	`type` varchar(255),
	PRIMARY KEY (`id`),
	INDEX  (`user_id`) comment '',
	INDEX  (`contact_id`) comment '',
	INDEX  (`created_at`) comment ''
) COMMENT='';