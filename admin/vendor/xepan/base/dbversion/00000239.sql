CREATE TABLE `document_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) DEFAULT NULL,
  `head` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `is_valid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  KEY `head` (`head`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8