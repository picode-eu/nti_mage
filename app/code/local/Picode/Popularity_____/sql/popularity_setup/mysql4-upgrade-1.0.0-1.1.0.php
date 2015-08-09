<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('popularity_details')};

    CREATE TABLE IF NOT EXISTS {$this->getTable('popularity_details')} (
        `detail_id` int(11) unsigned NOT NULL auto_increment,
        `popularity_id` int(11) unsigned NOT NULL,
        `popularity_type` varchar(11) NULL,
        `customer_id` int(11) unsigned NOT NULL default '0',
        `customer_ip` varchar(16) NULL,
        `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`detail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// CONSTRAINT `popularity_offer_details` FOREIGN KEY (`popularity_id`) REFERENCES `{$this->getTable('popularity_offer')}` (`popularity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
// CONSTRAINT `popularity_portfolio_details` FOREIGN KEY (`popularity_id`) REFERENCES `{$this->getTable('popularity_portfolio')}` (`popularity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
// CONSTRAINT `popularity_provider_details` FOREIGN KEY (`popularity_id`) REFERENCES `{$this->getTable('popularity_provider')}` (`popularity_id`) ON DELETE CASCADE ON UPDATE CASCADE

$installer->endSetup();