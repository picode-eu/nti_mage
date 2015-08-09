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
                              // 'checkout_register',
                              'customer_account_edit',
                              // 'customer_account_create',
                              // 'adminhtml_checkout',
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
                              // 'checkout_register',
                              'customer_account_edit',
                              // 'customer_account_create',
                              // 'adminhtml_checkout',
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
                              // 'checkout_register',
                              'customer_account_edit',
                              // 'customer_account_create',
                              // 'adminhtml_checkout',
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

/* main table ****************************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('providerreputation_reputation')} (
        `reputation_id` int(11) unsigned NOT NULL auto_increment,
        `provider_id` int(11) unsigned NOT NULL default '0',
        `provider_credit` int(11) unsigned NOT NULL default '0',
        `provider_reputation` int(11) unsigned NOT NULL default '0',
        `provider_views` int(11) unsigned NOT NULL default '0',
        `provider_contacts` int(11) unsigned NOT NULL default '0',
        `provider_loves` int(11) unsigned NOT NULL default '0',
        `provider_facebook` int(11) unsigned NOT NULL default '0',
        `provider_gplus` int(11) unsigned NOT NULL default '0',
        `provider_tweets` int(11) unsigned NOT NULL default '0',
        `provider_emails` int(11) unsigned NOT NULL default '0',
        `provider_ratings` int(11) unsigned NOT NULL default '0',
        `provider_comments` int(11) unsigned NOT NULL default '0',
        `provider_verified` int(1) unsigned NOT NULL default '0',
        `verification_date` datetime NULL default NULL,
        `created_at` timestamp NOT NULL on update CURRENT_TIMESTAMP,
        `updated_at` datetime NULL default NULL,
        PRIMARY KEY (`reputation_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/* referd tables ***************************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('providerreputation_views')} (
        `view_id` int(11) unsigned NOT NULL auto_increment,
        `provider_id` int(11) unsigned NOT NULL default '0',
        `customer_identifier` varchar(20) NULL DEFAULT NULL,
        `customer_ip` varchar(15) NULL default NULL,
        `created_at` timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`view_id`)
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
    CREATE TABLE IF NOT EXISTS {$this->getTable('providerreputation_contacts')} (
        `contact_id` int(11) unsigned NOT NULL auto_increment,
        `provider_id` int(11) unsigned NOT NULL default '0',
        `customer_identifier` varchar(20) NULL DEFAULT NULL,
        `customer_ip` varchar(15) NULL default NULL,
        `created_at` timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`contact_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// $installer->getConnection()
    // ->addKey(
        // $this->getTable('providerreputation_contacts'),
        // 'IDX_CONTACTS', 
        // 'provider_id'
    // );
//     
// $installer->getConnection()
    // ->addConstraint(
        // 'FK_ITEMS_RELATION_CONTACTS',
        // $this->getTable('providerreputation_contacts'),
        // 'provider_id',
        // $this->getTable('providerreputation_reputation'),
        // 'provider_id',
        // 'cascade',
        // 'cascade',
        // 'false'
// );

/*********************************************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('providerreputation_emails')} (
        `email_id` int(11) unsigned NOT NULL auto_increment,
        `provider_id` int(11) unsigned NOT NULL default '0',
        `customer_identifier` varchar(20) NULL DEFAULT NULL,
        `customer_ip` varchar(15) NULL default NULL,
        `receiver_email` varchar(254) NULL DEFAULT NULL,
        `sendemail_blocked` INT(1) UNSIGNED NOT NULL DEFAULT '0',
        `created_at` timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`email_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/*********************************************************************************************************/

$installer->endSetup();