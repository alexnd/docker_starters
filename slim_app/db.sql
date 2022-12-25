CREATE TABLE `users` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`role` int(11) unsigned NOT NULL,
`username` varchar(40) NOT NULL,
`password` varchar(40) NOT NULL,
`token` varchar(40) NOT NULL,
`created_at` int(10) unsigned NOT NULL,
`expires_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_token` (`token`),
KEY `expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_tokens` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`user_agent` varchar(40) NOT NULL,
`token` varchar(40) NOT NULL,
`created_at` int(10) unsigned NOT NULL,
`expires_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uniq_token` (`token`),
KEY `fk_user_id` (`user_id`),
KEY `expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `files` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`uri` varchar(128) NOT NULL,
`info` varchar(200) DEFAULT NULL,
`hash` varchar(32) DEFAULT NULL,
`user_id` int(11) DEFAULT NULL,
`created_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contents` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned DEFAULT NULL,
`file_id` int(11) unsigned DEFAULT NULL,
`page_id` int(11) unsigned DEFAULT NULL,
`category_id` int(11) unsigned DEFAULT NULL,
`product_id` int(11) unsigned DEFAULT NULL,
`uri` varchar(255) DEFAULT NULL,
`title` varchar(200) DEFAULT NULL,
`body` text DEFAULT NULL,
`hash` varchar(32) default NULL,
`created_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pages` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`uri` varchar(255) NOT NULL,
`body` text DEFAULT NULL,
`hash` varchar(32) default NULL,
`title` varchar(255) DEFAULT NULL,
`created_at` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


