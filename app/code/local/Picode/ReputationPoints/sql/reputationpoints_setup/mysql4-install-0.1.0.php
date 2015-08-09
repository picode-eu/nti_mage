<?php
$installer = $this;
$installer->startSetup();

/*
 * create new furnizor attributes
**/

$customerEntityType = $installer->getEntityTypeId('customer');
$customerAttributeSetId = $installer->getDefaultAttributeSetId($customerEntityType);
$customerAttributeGroupId = $installer->getDefaultAttributeGroupId($customerEntityType, $customerAttributeSetId);

$installer->addAttribute('customer', 'provider_credits', array(
    'type'  => 'int',
    'label' => 'Credits',
    'input' => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'user_defined' => true,
    'note'     => '',
));

$installer->addAttributeToGroup(
    $customerEntityType,
    $customerAttributeSetId,
    $customerAttributeGroupId,
    'provider_credits',
    0
);

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'provider_credits');
$attribute->setData('used_in_forms',
                            array(
                              'adminhtml_customer',
                              'customer_account_edit',
                            )
                     )
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', false)
            ->setData('is_user_defined', true)
            ->setData('is_visible', true)
            ->setData('sort_order', 10);
            
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'provider_reputation', array(
    'type'  => 'int',
    'label' => 'Reputation',
    'input' => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'user_defined' => true,
    'note'     => '',
));

$installer->addAttributeToGroup(
    $customerEntityType,
    $customerAttributeSetId,
    $customerAttributeGroupId,
    'provider_reputation',
    0
);

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'provider_reputation');
$attribute->setData('used_in_forms',
                            array(
                              'adminhtml_customer',
                              'customer_account_edit',
                            )
                     )
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', false)
            ->setData('is_user_defined', true)
            ->setData('is_visible', true)
            ->setData('sort_order', 15);
            
$attribute->save();



$installer->addAttribute('customer', 'provider_views', array(
    'type'  => 'int',
    'label' => 'Profile Views',
    'input' => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'user_defined' => true,
    'note'     => '',
));

$installer->addAttributeToGroup(
    $customerEntityType,
    $customerAttributeSetId,
    $customerAttributeGroupId,
    'provider_views',
    0
);

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'provider_views');
$attribute->setData('used_in_forms',
                            array(
                              'adminhtml_customer',
                              'customer_account_edit',
                            )
                     )
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', false)
            ->setData('is_user_defined', true)
            ->setData('is_visible', true)
            ->setData('sort_order', 20);
            
$attribute->save();

/*
 * create module's tables
**/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_identifier')} (
        `identifier_id`       int(11) unsigned NOT NULL auto_increment,
        `customer_identifier` varchar(32) NULL default NULL,
        `is_provider`         int(1) unsigned NOT NULL default '0',
        `customer_id`         int(11) unsigned NULL default NULL,
        `customer_ip`         varchar(15) NULL DEFAULT NULL,
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
        `last_login`          datetime NOT NULL,
        `updated_at`          datetime NOT NULL,
        
        PRIMARY KEY (`identifier_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// $installer->getConnection()
    // ->addKey(
        // $this->getTable('providerreputation_views'),
        // 'IDX_VIEWS', 
        // 'provider_id'
    // );
//     
// $installer->getConnection()
    // ->addConstraint(
        // 'FK_ITEMS_RELATION_VIWES',
        // $this->getTable('providerreputation_views'),
        // 'provider_id',
        // $this->getTable('providerreputation_reputation'),
        // 'provider_id',
        // 'cascade',
        // 'cascade',
        // 'false'
// );

/*********************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_reputation')} (
        `reputation_id`       int(11) unsigned NOT NULL auto_increment,
        `customer_id`         int(11) unsigned NOT NULL,
        `customer_identifier` varchar(32) NULL default NULL,
        `earned_points`       int(11) unsigned NOT NULL default '0',
        `is_verified`         int(1) unsigned NOT NULL default '0',
        `verification_date`   datetime NULL default NULL,
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
        `updated_at`          datetime NOT NULL,
        
        PRIMARY KEY (`reputation_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_provider')} (
        `rppprovider_id`      int(11) unsigned NOT NULL auto_increment,
        `provider_id`         int(11) unsigned NOT NULL,
        `customer_identifier` varchar(32) NULL default NULL,
        `reputation_points`   int(11) unsigned NOT NULL default '0',
        `view_count`          int(11) unsigned NOT NULL default '0',
        `frdemail_count`      int(11) unsigned NOT NULL default '0',
        `ctcemail_count`      int(11) unsigned NOT NULL default '0',
        `facebook_count`      int(11) unsigned NOT NULL default '0',
        `twitter_count`       int(11) unsigned NOT NULL default '0',
        `gplus_count`         int(11) unsigned NOT NULL default '0',
        `instagram_count`     int(11) unsigned NOT NULL default '0',
        `linkedin_count`      int(11) unsigned NOT NULL default '0',
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
        `updated_at`          datetime NOT NULL,
        
        PRIMARY KEY (`rppprovider_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_offer')} (
        `rppoffer_id`         int(11) unsigned NOT NULL auto_increment,
        `provider_id`         int(11) unsigned NOT NULL,
        `customer_identifier` varchar(32) NULL default NULL,
        `reputation_points`   int(11) unsigned NOT NULL default '0',
        `view_count`          int(11) unsigned NOT NULL default '0',
        `frdemail_count`      int(11) unsigned NOT NULL default '0',
        `ctcemail_count`      int(11) unsigned NOT NULL default '0',
        `facebook_count`      int(11) unsigned NOT NULL default '0',
        `twitter_count`       int(11) unsigned NOT NULL default '0',
        `gplus_count`         int(11) unsigned NOT NULL default '0',
        `instagram_count`     int(11) unsigned NOT NULL default '0',
        `linkedin_count`      int(11) unsigned NOT NULL default '0',
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
        `updated_at`          datetime NOT NULL,
        
        PRIMARY KEY (`rppoffer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_details')} (
        `rppdetails_id`       int(11) unsigned NOT NULL auto_increment,
        `customer_identifier` varchar(32) NULL default NULL,
        `rpp_type`            varchar(32) NULL default NULL,
        `entity_id`           int(11) unsigned NOT NULL default '0',
        `updated_rpp`         varchar(32) NULL default NULL,
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,
        
        PRIMARY KEY (`rppdetails_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('rpp_frdemails')} (
        `rppemails_id`        int(11) unsigned NOT NULL auto_increment,
        `customer_identifier` varchar(32) NULL default NULL,
        `entity_id`           int(11) unsigned NOT NULL default '0',
        `rpp_type`            varchar(32) NULL default NULL,
        `sender_id`           int(11) UNSIGNED NULL DEFAULT NULL,
        `sender_firstname`    varchar(64) NULL default NULL,
        `sender_lastname`     varchar(64) NULL default NULL,
        `sender_email`        varchar(64) NULL default NULL,
        `receiver_id`         int(11) UNSIGNED NULL DEFAULT NULL,
        `receiver_email`      varchar(64) NULL default NULL,
        `subject`             varchar(255) NULL default NULL,
        `message`             text NULL default NULL,
        `created_at`          timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL default CURRENT_TIMESTAMP,

        PRIMARY KEY (`rppemails_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************/

$installer->endSetup();