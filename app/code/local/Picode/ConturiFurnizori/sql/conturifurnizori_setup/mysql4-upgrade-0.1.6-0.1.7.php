<?php

$installer = $this;
$installer->startSetup();

/*
 * create new business attributes
**/

$installer->addAttribute('customer', 'business_images_logo',  array(
    'type'     => 'varchar',
    'label'    => 'Company Logo',
    'input'    => 'file',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_images_logo');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 5); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_images_coperta',  array(
    'type'     => 'varchar',
    'label'    => 'Cover Image',
    'input'    => 'file',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_images_coperta');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 10); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_descriptions_title',  array(
    'type'     => 'varchar',
    'label'    => 'Company Title',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_title');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 15); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_descriptions_slogan',  array(
    'type'     => 'varchar',
    'label'    => 'Slogan',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_slogan');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 20); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_descriptions_exp',  array(
    'type'     => 'varchar',
    'label'    => 'Experience',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_experience',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_exp');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 25); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_descriptions_desc',  array(
    'type'     => 'text',
    'label'    => 'Description',
    'input'    => 'textarea',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_desc');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 30); 
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_descriptions_aparat',  array(
    'type'     => 'text',
    'label'    => 'Equipment',
    'input'    => 'textarea',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_aparat');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 35); 
    
$attribute->save();

/********************************************************************************************/

/**
 * account attributes
 */
/*
$installer->addAttribute('customer', 'business_account_test',  array(
    'type'     => 'varchar',
    'label'    => 'Test',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_account_test');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 100); 
    
$attribute->save();
*/

/********************************************************************************************/

$installer->endSetup();