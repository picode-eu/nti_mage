<?php
$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('portofoliu_visitors')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('portofoliu_visitors')} (
		`visitor_id` int(11) NOT NULL auto_increment,
		`customer_id` int(11) NULL default NULL,
		`customer_identifier` varchar(32) NOT NULL,
		`portofoliu_type` varchar(11) NOT NULL,
		`portofoliu_id` int(11) NOT NULL,
		`created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
		PRIMARY KEY (`visitor_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
	 