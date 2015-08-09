<?php

$installer = $this;
$installer->startSetup();

/**
 * add new customer group
**/
$customerGroupModel = Mage::getModel('customer/group');
$newCustomerGroupCode = 'Furnizori';
$groupExists = false;

// check if the customer group already exists or not
$customerGroupCollection = $customerGroupModel->getCollection();

foreach ($customerGroupCollection as $group) {
    if ($group['customer_group_code'] == $newCustomerGroupCode) {
        // if group was faund stop further checks
        $groupExists = true;
        break;
    }
}

// create customer group if not exists
if (!$groupExists) {
    $customerGroupModel->setCode($newCustomerGroupCode);
    $customerGroupModel->setTaxClassId(3);
    $customerGroupModel->save();
}

/*
 * create new furnizor attributes
**/

$installer->addAttribute('customer', 'furnizor_account_status',  array(
    'type'     => 'int',
    'label'    => 'Account Status',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_status',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_status');
        
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
    ->setData('sort_order', 4);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_account_type',  array(
    'type'     => 'int',
    'label'    => 'Account Type',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_acctype',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_type');
        
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
    ->setData('sort_order', 6);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_account_trial_exp',  array(
    'input'    => 'date',
    'type'     => 'datetime',
    'label'    => 'Trial Expiration Date',
    'backend'  => 'eav/entity_attribute_backend_datetime',
    'visible'  => true,
    'required' => false,
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default'  => '',
    'note'     => ''
    ));
    
$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_trial_exp');
        
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
    ->setData('sort_order', 7);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_account_expiration',  array(
    'input'    => 'date',
    'type'     => 'datetime',
    'label'    => 'Expiration Date',
    'backend'  => 'eav/entity_attribute_backend_datetime',
    'visible'  => true,
    'required' => false,
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'default'  => '',
    'note'     => ''
    ));
    
$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_expiration');
        
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
    ->setData('sort_order', 8);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_company_name',  array(
    'type'     => 'varchar',
    'label'    => 'Company Name',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_name');
        
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

$installer->addAttribute('customer', 'furnizor_company_type',  array(
    'type'     => 'int',
    'label'    => 'Organization Type',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_type',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_type');
        
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

$installer->addAttribute('customer', 'furnizor_company_services',  array(
    'type'     => 'int',
    'label'    => 'Services',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_services',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_services');
        
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

$installer->addAttribute('customer', 'furnizor_location_province',  array(
    'type'     => 'int',
    'label'    => 'State/Province',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_province',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_province');
        
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

$installer->addAttribute('customer', 'furnizor_location_city',  array(
    'type'     => 'varchar',
    'label'    => 'City',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_city');
        
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

$installer->addAttribute('customer', 'furnizor_location_address',  array(
    'type'     => 'varchar',
    'label'    => 'Street (Address)',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_address');
        
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
    ->setData('sort_order', 40);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_location_zip',  array(
    'type'     => 'varchar',
    'label'    => 'Zip/Postal Code',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_zip');
        
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
    ->setData('sort_order', 45);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_contact_firstname',  array(
    'type'     => 'varchar',
    'label'    => 'Contact First Name',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_firstname');
        
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
    ->setData('sort_order', 50);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_contact_lastname',  array(
    'type'     => 'varchar',
    'label'    => 'Contact Last Name',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_lastname');
        
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
    ->setData('sort_order', 55);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_contact_email',  array(
    'type'     => 'varchar',
    'label'    => 'Contact Email',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_email');
        
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
    ->setData('sort_order', 60);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'furnizor_contact_phone',  array(
    'type'     => 'varchar',
    'label'    => 'Contact Phone',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_phone');
        
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
    ->setData('sort_order', 65); 
    
$attribute->save();

/********************************************************************************************/

$installer->endSetup();