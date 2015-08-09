<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_tip',
    'varchar(255) NULL DEFAULT NULL AFTER `fax`'
);

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_tip', array(
    'type' => 'int',
    'input' => 'select',
    'label' => 'Forma de faturare',
    'source' => 'conturifurnizori/attribute_source_facturare',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_tip');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 5);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_cui', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Cod Unic de Inregistrare (CUI)',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_cui');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 10);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_nrc', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Nr. Reg. Comertului',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_nrc');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 15);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_banca', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Banca',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_banca');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 20);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_iban', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'IBAN',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_iban');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 25);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_ci', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Serie si numar CI',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_ci');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 30);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'billing_cnp', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'CNP',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'billing_cnp');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 35);

$attribute->save();

/***************************************************************************************************/

$this->addAttribute('customer_address', 'street_other', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Alte detalii utile',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'default' => '',
    'user_defined' => true,
    'visible_on_front' => true
	
));

$attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'street_other');
$used_in_forms   = array();
$used_in_forms[] = 'customer_register_address';
$used_in_forms[] = 'customer_address_edit';
$used_in_forms[] = 'adminhtml_customer_address';

$attribute->setData('used_in_forms', $used_in_forms)
	->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 40);

$attribute->save();

/***************************************************************************************************/

$installer->endSetup();