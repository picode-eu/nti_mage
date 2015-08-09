<?php
$installer = $this;
$installer->startSetup();

/*
 * create new furnizor attributes
**/

$customerEntityType = $installer->getEntityTypeId('customer');
$customerAttributeSetId = $installer->getDefaultAttributeSetId($customerEntityType);
$customerAttributeGroupId = $installer->getDefaultAttributeGroupId($customerEntityType, $customerAttributeSetId);

$installer->addAttribute('customer', 'customer_identifier', array(
    'type'  => 'varchar',
    'label' => 'Unique Identifier',
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
    'customer_identifier',
    0
);

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_identifier');
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
            ->setData('sort_order', 0);
            
$attribute->save();

/********************************************************************************************/

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('customeridentifier_ipidassociation')} (
        `id` int(11) unsigned NOT NULL auto_increment,
        `customer_identifier` varchar(20) NOT NULL,
        `customer_ip` varchar(20) NOT NULL,
        `created_at` timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/********************************************************************************************/

$installer->endSetup();