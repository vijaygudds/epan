CREATE TABLE `list_my_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL ,
  `list_data_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;