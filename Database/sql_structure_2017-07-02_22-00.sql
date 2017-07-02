DROP TABLE IF EXISTS `color_schemes`;

CREATE TABLE `color_schemes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(256) DEFAULT NULL,
  `class` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `matches`;

CREATE TABLE `matches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` varchar(256) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `opponent_id` int(11) DEFAULT NULL,
  `quick_access_code` varchar(5) DEFAULT NULL,
  `field` text,
  `color_scheme_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1' COMMENT '1 = Playing, 2 = Creator won, 3 = Opponent won, 4 = Creator surrendered, 5 = Opponent surrendered',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `active_player_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `registration_token` varchar(256) DEFAULT NULL,
  `activated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
