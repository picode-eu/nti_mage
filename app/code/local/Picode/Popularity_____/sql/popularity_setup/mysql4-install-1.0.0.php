<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('popularity_offer')};

    CREATE TABLE IF NOT EXISTS {$this->getTable('popularity_offer')} (
        `popularity_id` int(11) unsigned NOT NULL auto_increment,
        `offer_id` int(11) unsigned NOT NULL default '0',
        `views` int(11) unsigned NOT NULL default '0',
        `contact_views` int(11) unsigned NOT NULL default '0',
        `contacts` int(11) unsigned NOT NULL default '0',
        `loves` int(11) unsigned NOT NULL default '0',
        `ratings` int(11) unsigned NOT NULL default '0',
        `comments` int(11) unsigned NOT NULL default '0',
        `fb_likes` int(11) unsigned NOT NULL default '0',
        `fb_shares` int(11) unsigned NOT NULL default '0',
        `tweetes` int(11) unsigned NOT NULL default '0',
        `earned_points` int(11) unsigned NOT NULL default '0',
        `updated_at` timestamp NOT NULL on update CURRENT_TIMESTAMP,
        PRIMARY KEY (`popularity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('popularity_portfolio')};

    CREATE TABLE IF NOT EXISTS {$this->getTable('popularity_portfolio')} (
        `popularity_id` int(11) unsigned NOT NULL auto_increment,
        `portfolio_id` int(11) unsigned NOT NULL default '0',
        `views` int(11) unsigned NOT NULL default '0',
        `contact_views` int(11) unsigned NOT NULL default '0',
        `contacts` int(11) unsigned NOT NULL default '0',
        `loves` int(11) unsigned NOT NULL default '0',
        `ratings` int(11) unsigned NOT NULL default '0',
        `comments` int(11) unsigned NOT NULL default '0',
        `fb_likes` int(11) unsigned NOT NULL default '0',
        `fb_shares` int(11) unsigned NOT NULL default '0',
        `tweetes` int(11) unsigned NOT NULL default '0',
        `earned_points` int(11) unsigned NOT NULL default '0',
        `updated_at` timestamp NOT NULL on update CURRENT_TIMESTAMP,
        PRIMARY KEY (`popularity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('popularity_provider')};

    CREATE TABLE IF NOT EXISTS {$this->getTable('popularity_provider')} (
        `popularity_id` int(11) unsigned NOT NULL auto_increment,
        `provider_id` int(11) unsigned NOT NULL default '0',
        `views` int(11) unsigned NOT NULL default '0',
        `contact_views` int(11) unsigned NOT NULL default '0',
        `contacts` int(11) unsigned NOT NULL default '0',
        `loves` int(11) unsigned NOT NULL default '0',
        `ratings` int(11) unsigned NOT NULL default '0',
        `comments` int(11) unsigned NOT NULL default '0',
        `fb_likes` int(11) unsigned NOT NULL default '0',
        `fb_shares` int(11) unsigned NOT NULL default '0',
        `tweetes` int(11) unsigned NOT NULL default '0',
        `earned_points` int(11) unsigned NOT NULL default '0',
        `updated_at` timestamp NOT NULL on update CURRENT_TIMESTAMP,
        PRIMARY KEY (`popularity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();