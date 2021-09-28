DROP TABLE IF EXISTS `agency`;
CREATE TABLE `agency` (
  `contact_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_channelpartner` tinyint(4) DEFAULT NULL,
  `channelpartner_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;