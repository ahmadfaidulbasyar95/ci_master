SET foreign_key_checks = 0;
DROP TABLE IF EXISTS `_config`;
CREATE TABLE `_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



SET foreign_key_checks = 1;
