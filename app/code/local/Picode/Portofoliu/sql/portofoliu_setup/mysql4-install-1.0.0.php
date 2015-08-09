<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('portofoliu_albums')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('portofoliu_albums')} (
		`album_id` int(11) NOT NULL auto_increment,
		`customer_id` int(11) NOT NULL,
		`album_name` varchar(255),
		`album_description` text,
		`album_cover` varchar(255) NULL,
		`is_visible` int(1) NOT NULL default 0,
		`visit_count` int(11) NOT NULL default 0,
		`created_at` timestamp NULL default NULL,
		`updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
		PRIMARY KEY (`album_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('portofoliu_videos')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('portofoliu_videos')} (
		`video_id` int(11) NOT NULL auto_increment,
		`customer_id` int(11) NOT NULL,
		`video_name` varchar(255),
		`video_description` text,
		`video_url` varchar(255),
		`is_visible` int(1) NOT NULL default 0,
		`visit_count` int(11) NOT NULL default 0,
		`created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
		PRIMARY KEY (`video_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('portofoliu_photos')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('portofoliu_photos')} (
		`photo_id` int(11) NOT NULL auto_increment,
		`album_id` int(11) NOT NULL,
		`photo_label` varchar(255),
		`photo_url` varchar(20) NOT NULL,
		`added_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
		PRIMARY KEY (`photo_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
	 