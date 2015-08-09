<?php

$installer = $this;
$installer->startSetup();

/*
 * create new furnizor attributes
**/

$installer->addAttribute('customer', 'furnizor_company_zone',  array(
    'type'     => 'int',
    'label'    => 'Zona de activitate',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_zone',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_zone');
        
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
    ->setData('sort_order', 26); 
    
$attribute->save();

/**************************************************************************************************/

$installer->addAttribute('customer', 'furnizor_company_cstzone',  array(
    'type'     => 'varchar',
    'label'    => 'Zona Personalizata',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_zone');
        
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
    ->setData('sort_order', 27); 
    
$attribute->save();


$installer->endSetup();